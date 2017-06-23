import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { Set } from 'immutable';
import { Observable } from 'rxjs';
import 'rxjs/add/observable/dom/ajax';
import { push } from 'react-router-redux';
import { Takedown } from '../entity';
import * as TakedownActions from '../actions/takedown';

export function fetchTakedownList( action$, store ) {
	return action$.ofType( 'TAKEDOWN_LIST_FETCH' )
		.flatMap( () => {
			return Observable.ajax( {
				url: '/api/takedown?page=' + store.getState().takedown.page,
				responseType: 'json',
				headers: {
					Authorization: 'Bearer ' + store.getState().token
				}
			} ).map( ( ajaxResponse ) => {
				const takedowns = ajaxResponse.response.map( ( item ) => {
					return new Takedown( item );
				} );

				return TakedownActions.addMultiple( takedowns );
			} )
				.catch( () => {
					return Observable.of( TakedownActions.addMultiple( [] ) );
				} );
		} );
}

export function fetchTakedown( action$, store ) {
	return action$.ofType( 'TAKEDOWN_FETCH' )
		.switchMap( ( action ) => {
			return Observable.ajax( {
				url: '/api/takedown/' + action.id,
				responseType: 'json',
				headers: {
					Authorization: 'Bearer ' + store.getState().token
				}
			} )
				.map( ( ajaxResponse ) => {
					const takedown = new Takedown( ajaxResponse.response );

					return TakedownActions.add( takedown );
				} )
				.catch( ( ajaxError ) => {
					const takedown = new Takedown( {
						id: action.id,
						error: ajaxError.status,
						status: 'error'
					} );

					return Observable.of( TakedownActions.add( takedown ) );
				} );
		} );
}

export function takedownSave( action$, store ) {
	return action$.ofType( 'TAKEDOWN_CREATE_SAVE' )
		.switchMap( () => {
			let takedown = store.getState().takedown.create,
				involvedNames = [];

			// Prepare takedown for saving.
			takedown = takedown.set( 'status', undefined );
			takedown = takedown.set( 'error', undefined );

			// We must split out the user names because of T168571
			involvedNames = takedown.involvedIds.map( ( id ) => {
				return store.getState().user.list.find( ( user ) => {
					return user.id === id;
				} );
			} ).filter( ( user ) => {
				return !!user;
			} ).map( ( user ) => {
				return user.username;
			} );

			involvedNames = new Set( involvedNames );

			takedown = takedown.set( 'involvedNames', involvedNames );
			takedown = takedown.set( 'involvedIds', undefined );

			return Observable.ajax( {
				url: '/api/takedown',
				method: 'POST',
				body: JSON.stringify( takedown.toJS() ),
				responseType: 'json',
				headers: {
					'Content-Type': 'application/json',
					Authorization: 'Bearer ' + store.getState().token
				}
			} )
				.flatMap( ( ajaxResponse ) => {
					const takedown = new Takedown( ajaxResponse.response );

					return Observable.concat(
						Observable.of( TakedownActions.add( takedown ) ),
						Observable.of( push( '/takedown/' + takedown.id ) ),
						Observable.of( TakedownActions.clearCreate() )
					);
				} )
				.catch( ( ajaxError ) => {
					// Set the takedown state.
					const takedown = store.getState().takedown.create.set( 'status', 'error' ).set( 'error', ajaxError.status );
					return Observable.of( TakedownActions.updateCreate( takedown ) );
				} );
		} );
}
