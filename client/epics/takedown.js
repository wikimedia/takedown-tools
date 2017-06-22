import 'rxjs/add/operator/switchMap';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { Observable } from 'rxjs';
import 'rxjs/add/observable/dom/ajax';
import { Takedown } from '../entity';
import * as TakedownActions from '../actions/takedown';
import * as moment from 'moment';

export function fetchTakedownList( action$, store ) {
	return action$.ofType( 'TAKEDOWN_LIST_FETCH' )
		.switchMap( () => {
			return Observable.ajax( {
				url: '/api/takedown',
				responseType: 'json',
				headers: {
					Authorization: 'Bearer ' + store.getState().token
				}
			} ).map( ( ajaxResponse ) => {
				const takedowns = ajaxResponse.response.map( ( item ) => {
					return new Takedown( {
						...item,
						created: moment.utc( item.created )
					} );
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
			return Observable.ajax( {
				url: '/api/takedown',
				method: 'POST',
				body: JSON.stringify( store.getState().takedown.create.set( 'status', undefined ).set( 'error', undefined ).toJS() ),
				responseType: 'json',
				headers: {
					'Content-Type': 'application/json',
					Authorization: 'Bearer ' + store.getState().token
				}
			} )
				.map( ( ajaxResponse ) => {
					const takedown = new Takedown( ajaxResponse.response );

					// @TODO clear out the created takedown and redirect
					//       the user.
					return TakedownActions.add( takedown );
				} )
				.catch( ( ajaxError ) => {
					// Set the takedown state.
					const takedown = store.getState().takedown.create.set( 'status', 'error' ).set( 'error', ajaxError.status );
					return Observable.of( TakedownActions.updateCreate( takedown ) );
				} );
		} );
}
