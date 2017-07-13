import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { Set } from 'immutable';
import { Observable } from 'rxjs';
import 'rxjs/add/observable/dom/ajax';
import { push } from 'react-router-redux';
import { Takedown } from 'entities/takedown/takedown';
import { MetadataSet } from 'entities/metadata.set';
import * as TakedownActions from 'actions/takedown';
import * as TokenActions from 'actions/token';
import { defaultCommonsText, defaultCommonsVillagePumpText, defaultUserNoticeText } from 'utils';

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
				.catch( ( ajaxError ) => {
					if ( ajaxError.status === 401 ) {
						return Observable.of( TokenActions.tokenRemove() );
					}

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
					if ( ajaxError.status === 401 ) {
						return Observable.of( TokenActions.tokenRemove() );
					}

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
				invovled,
				involvedNames = [],
				metadataIds,
				removeType;

			invovled = takedown.involvedIds.map( ( id ) => {
				return store.getState().user.list.find( ( user ) => {
					return user.id === id;
				} );
			} ).filter( ( user ) => {
				return !!user;
			} );

			invovled = new Set( invovled );

			// Prepare takedown for saving.
			takedown = takedown.set( 'status', undefined );
			takedown = takedown.set( 'error', undefined );

			// Remove whichever type this takedown is *not*
			if ( takedown.type ) {
				switch ( takedown.type ) {
					case 'dmca':
						// If the wiki text was not modified, apply the default text.
						if ( takedown.dmca.commonsSend ) {
							takedown = takedown.setIn( [ 'dmca', 'commonsTitle' ], takedown.dmca.commonsTitle || takedown.dmca.wmfTitle );
							takedown = takedown.setIn( [ 'dmca', 'commonsText' ], takedown.dmca.commonsText || defaultCommonsText( takedown.dmca.commonsTitle, takedown.dmca.wmfTitle, takedown.dmca.pageIds ) );
						}
						if ( takedown.dmca.commonsVillagePumpSend ) {
							takedown = takedown.setIn( [ 'dmca', 'commonsVillagePumpText' ], takedown.dmca.commonsVillagePumpText || defaultCommonsVillagePumpText( takedown.dmca.commonsTitle, takedown.dmca.wmfTitle, takedown.dmca.pageIds ) );
						}
						if ( takedown.dmca.userNotices.size > 0 ) {
							takedown = takedown.setIn( [ 'dmca', 'userNotices' ], takedown.dmca.userNotices.filter( ( user ) => {
								// Ensure that notices are only sent to those who are invovled.
								return !!takedown.involvedIds.find( ( id ) => id === user.id );
							} ).map( ( user ) => {
								return user.set( 'notice', user.notice || defaultUserNoticeText( user.username, takedown.dmca.pageIds ) );
							} ) );
						}
						if ( takedown.dmca.wmfTitle ) {
							takedown = takedown.setIn( [ 'dmca', 'wmfTitle' ], 'DMCA_' + takedown.dmca.wmfTitle.replace( / /g, '_' ) );
						}
						removeType = 'cp';
						break;
					case 'cp':
						removeType = 'dmca';
						takedown = takedown.setIn( [ 'cp', 'approverId' ], undefined );
						takedown = takedown.setIn( [ 'cp', 'files' ], takedown.cp.files.map( ( file ) => {
							return file.set( 'id', undefined );
						} ) );
						break;
				}

				takedown = takedown.set( removeType, undefined );
				metadataIds = takedown.metadataIds.filter( ( id ) => {
					const meta = MetadataSet.find( ( metadata ) => {
						return metadata.id === id;
					} );

					if ( !meta ) {
						return false;
					}

					return meta.type === takedown.type;
				} );
				takedown = takedown.set( 'metadataIds', metadataIds );

				// Remove the type.
				takedown = takedown.remove( 'type' );
			}

			// We must split out the user names because of T168571
			involvedNames = invovled.map( ( user ) => {
				return user.username;
			} );

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
					if ( ajaxError.status === 401 ) {
						return Observable.of( TokenActions.tokenRemove() );
					}

					// Set the takedown state.
					const takedown = store.getState().takedown.create.set( 'status', 'error' ).set( 'error', ajaxError.status );
					return Observable.of( TakedownActions.updateCreate( takedown ) );
				} );
		} );
}
