import { Observable } from 'rxjs';
import 'rxjs/add/operator/switchMap';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/debounceTime';
import 'rxjs/add/observable/dom/ajax';
import * as TokenActions from '../actions/token';

export function refreshToken( action$, store ) {
	return action$
		.filter( ( action ) => {
			const types = [
				'TOKEN_ADD',
				'TOKEN_SET',
				'TOKEN_REMOVE',
				'TOKEN_ERROR'
			];
			return !types.includes( action.type );
		} )
		.filter( () => {
			return !!store.getState().token;
		} )
		.debounceTime( 5 * 60 * 60 ) // 5 minutes.
		.flatMap( () => {
			return Observable.ajax( {
				url: '/api/token',
				responseType: 'json',
				headers: {
					Authorization: 'Bearer ' + store.getState().token
				}
			} )
				.map( ( ajaxResponse ) => {
					return TokenActions.tokenSet( ajaxResponse.response.token );
				} )
				.catch( ( ajaxError ) => {
					if ( ajaxError.status === 401 ) {
						return Observable.of( TokenActions.tokenRemove() );
					}

					return Observable.of( {
						type: 'TOKEN_ERROR',
						error: ajaxError.status
					} );
				} );
		} );
}
