import 'rxjs/add/operator/switchMap';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { Observable } from 'rxjs';
import 'rxjs/add/observable/dom/ajax';
import 'rxjs/add/observable/from';
import { Set } from 'immutable';
import { User } from '../entities/user';
import * as UserActions from '../actions/user';
import * as TokenActions from '../actions/token';

export function fetchUsers( action$, store ) {
	return action$.filter( ( action ) => {
		const types = [
			'TAKEDOWN_ADD_MULTIPLE',
			'TAKEDOWN_ADD'
		];

		return types.includes( action.type );
	} )
		.flatMap( ( action ) => {
			let ids = [],
				takedowns = [];

			// Get the takedowns that are being added.
			switch ( action.type ) {
				case 'TAKEDOWN_ADD':
					takedowns = [ action.takedown ];
					break;

				case 'TAKEDOWN_ADD_MULTIPLE':
					takedowns = [
						...action.takedowns
					];
					break;
			}

			// Get all of the referenced users.
			ids = takedowns.reduce( ( state, takedown ) => {
				const reporterIds = takedown.reporterId ? [ takedown.reporterId ] : [],
					approverIds = takedown.cp && takedown.cp.reporterId ? [ takedown.cp.reporterId ] : [];
				return [
					...state,
					...takedown.involvedIds,
					...reporterIds,
					...approverIds
				];
			}, [] );

			// Ensure they are unique.
			ids = new Set( ids );

			// Remove an ids that are already in the store.
			ids = ids.filter( ( id ) => {
				const found = store.getState().user.list.find( ( user ) => {
					return user.id === id;
				} );

				return !found;
			} );

			return Observable.from( ids.toArray() );
		} )
		.flatMap( ( id ) => {
			return Observable.ajax( {
				url: '/api/user/' + id,
				responseType: 'json',
				headers: {
					Authorization: 'Bearer ' + store.getState().token
				}
			} )
				.map( ( ajaxResponse ) => {
					return UserActions.add( new User( ajaxResponse.response ) );
				} )
				.catch( ( ajaxError ) => {
					if ( ajaxError.status === 401 ) {
						return Observable.of( TokenActions.tokenRemove() );
					}

					const user = new User( {
						id: id,
						error: ajaxError.status,
						status: 'error'
					} );

					return Observable.of( UserActions.add( user ) );
				} );
		} );
}
