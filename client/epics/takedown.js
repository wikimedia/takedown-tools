import 'rxjs/add/operator/switchMap';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs';
import 'rxjs/add/observable/dom/ajax';

export function fetchTakedownList( action$, store ) {
	return action$.ofType( 'TAKEDOWN_LIST_FETCH' )
		.switchMap( () => {
			return Observable.ajax( {
				url: window.location.origin + '/api/takedown',
				responseType: 'json',
				headers: {
					Authorization: 'Bearer ' + store.getState().token
				}
			} );
		} )
		.map( ( ajaxResponse ) => {
			return {
				type: 'TAKEDOWN_ADD_MULTIPLE',
				takedowns: ajaxResponse.response
			};
		} );
}
