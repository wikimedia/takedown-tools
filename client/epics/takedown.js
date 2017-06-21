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
						error: ajaxError.status
					} );

					return Observable.of( TakedownActions.add( takedown ) );
				} );
		} );
}
