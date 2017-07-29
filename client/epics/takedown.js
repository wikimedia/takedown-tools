import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { Set } from 'immutable';
import { Observable, Subject } from 'rxjs';
import 'rxjs/add/observable/dom/ajax';
import 'rxjs/add/operator/do';
import { push } from 'react-router-redux';
import { Takedown } from 'app/entities/takedown/takedown';
import { MetadataSet } from 'app/entities/metadata.set';
import { Captcha } from 'app/entities/captcha';
import { Post } from 'app/entities/takedown/dmca/post';
import { Error } from 'app/entities/error';
import * as TakedownActions from 'app/actions/takedown';
import * as TokenActions from 'app/actions/token';
import { defaultCommonsText, defaultCommonsVillagePumpText, defaultUserNoticeText, getWmfTitle } from 'app/utils';

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
						error: new Error( ajaxError.xhr.response ),
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
						if ( takedown.dmca.wmfTitle ) {
							takedown = takedown.setIn( [ 'dmca', 'wmfTitle' ], 'DMCA_' + takedown.dmca.wmfTitle.replace( / /g, '_' ) );
						}

						// Set default value(s).
						takedown = takedown.setIn( [ 'dmca', 'actionTakenId' ], takedown.dmca.actionTakenId || 'no' );

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
			} ).catch( ( ajaxError ) => {
				if ( ajaxError.status === 401 ) {
					return Observable.of( TokenActions.tokenRemove() );
				}

				// Set the takedown state. Use what is in the sotre since it might
				// have been updated since we started saving.
				const errorTakedown = store.getState().takedown.create.set( 'status', 'error' ).set( 'error', new Error( ajaxError.xhr.response ) );
				return Observable.of( TakedownActions.updateCreate( errorTakedown ) );
			} ).flatMap( ( ajaxResponse ) => {
				let response = new Takedown( ajaxResponse.response ),
					create = store.getState().takedown.create,
					add,
					error,
					uploads,
					finish;

				add = Observable.of( TakedownActions.add( response ) );

				finish = Observable.concat(
					Observable.of( push( '/takedown/' + response.id ) ),
					Observable.of( TakedownActions.clearCreate() )
				);

				if ( response.type === 'cp' ) {
					if ( create.cp.files.size > 0 ) {

						if ( create.cp.files.size !== response.cp.files.size ) {
							error = create.set( 'status', 'error' ).set( 'error', new Error( {
								message: 'File list does not match response.'
							} ) );
							return Observable.of( TakedownActions.updateCreate( error ) );
						}

						uploads = Observable.from( create.cp.files.toArray() )
							.flatMap( ( file, index ) => {
								let progressSubscriber,
									progress,
									serverFile,
									request;

								serverFile = response.cp.files.get( index );

								if ( serverFile.name !== file.name ) {
									throw new Error( {
										message: 'File names do not match'
									} );
								}

								progressSubscriber = new Subject();

								progress = progressSubscriber.map( ( event ) => {
									const percent = parseInt( ( event.loaded / event.total ) * 100 );

									create = store.getState().takedown.create;

									file = file.set( 'progress', percent );
									create = create.setIn( [ 'cp', 'files', index ], file );
									return TakedownActions.updateCreate( create );
								} );

								request = Observable.ajax( {
									url: `/api/takedown/${response.id}/ncmec/file/${serverFile.id}`,
									method: 'POST',
									body: file.file,
									progressSubscriber: progressSubscriber,
									responseType: 'json',
									headers: {
										Authorization: 'Bearer ' + store.getState().token,
										'Content-Type': file.file.type
									}
								} ).catch( ( ajaxError ) => {
									file = file.set( 'status', 'error' ).set( 'error', new Error( ajaxError.xhr.response ) );
									create = store.getState().takedown.create;
									create = create.setIn( [ 'cp', 'files', index ], file );
									return Observable.of( TakedownActions.updateCreate( create ) );
								} ).flatMap( ( uploadResponse ) => {
									response = new Takedown( uploadResponse.response );

									file = file.set( 'status', 'ready' );
									create = store.getState().takedown.create;
									create = create.setIn( [ 'cp', 'files', index ], file );
									return Observable.concat(
										Observable.of( TakedownActions.update( response ) ),
										Observable.of( TakedownActions.updateCreate( create ) )
									);
								} );

								// Return an observable that emits:
								// 1) The updated file status.
								// 2) The upload progress.
								// 3) The upload itself.
								file = file.set( 'status', 'uploading' );
								create = store.getState().takedown.create;
								create = create.setIn( [ 'cp', 'files', index ], file );
								return Observable.merge(
									Observable.of( TakedownActions.updateCreate( create ) ),
									progress,
									request
								);
							} );

						return Observable.concat(
							add,
							uploads,
							// @TODO add observable that finishes the report.
							finish
						);
					}
				}

				return Observable.concat(
					add,
					finish
				);
			} );
		} );
}

export function saveDmcaPost( action$, store ) {
	return action$.ofType( 'TAKEDOWN_DMCA_POST_SAVE' )
		.flatMap( ( action ) => {
			let post = action.takedown.dmca[ action.postName ],
				send,
				endPoint;

			// Prepare post for saving.
			post = post.set( 'title', undefined ).set( 'status', undefined );

			switch ( action.postName ) {
				case 'commonsPost':
					endPoint = 'commons';
					send = 'commonsSend';
					post = post.set( 'text', post.text || defaultCommonsText( post.title || getWmfTitle( action.takedown.dmca.wmfTitle ) || '', action.takedown.dmca.wmfTitle, action.takedown.pageIds ) );
					break;
				case 'commonsVillagePumpPost':
					endPoint = 'commons-village-pump';
					send = 'commonsVillagePumpSend';
					post = post.set( 'text', post.text || defaultCommonsVillagePumpText( post.title || getWmfTitle( action.takedown.dmca.wmfTitle ) || '', action.takedown.dmca.wmfTitle, action.takedown.pageIds ) );
					break;
			}

			// Prepare post for saving.
			post = post.set( 'title', undefined ).set( 'status', undefined );

			return Observable.ajax( {
				url: '/api/takedown/' + action.takedown.id + '/' + endPoint,
				method: 'POST',
				body: JSON.stringify( post.toJS() ),
				responseType: 'json',
				headers: {
					'Content-Type': 'application/json',
					Authorization: 'Bearer ' + store.getState().token
				}
			} )
				.map( ( ajaxResponse ) => {
					let takedown = store.getState().takedown.list.find( ( item ) => {
						return action.takedown.id === item.id;
					} );

					const response = new Takedown( ajaxResponse.response );

					if ( !takedown ) {
						return {
							type: 'ERROR'
						};
					}

					takedown = takedown.setIn( [ 'dmca', send ], response.dmca[ send ] );

					return TakedownActions.update( takedown );
				} )
				.catch( ( ajaxError ) => {
					let takedown = store.getState().takedown.list.find( ( item ) => {
						return action.takedown.id === item.id;
					} );

					if ( ajaxError.status === 409 && ajaxError.xhr.response.captcha ) {
						takedown = takedown.setIn( [ 'dmca', action.postName, 'status' ], 'captcha' );
						takedown = takedown.setIn( [ 'dmca', action.postName, 'captcha' ], new Captcha( ajaxError.xhr.response.captcha ) );
						return Observable.of( TakedownActions.update( takedown ) );
					}

					takedown = takedown.setIn( [ 'dmca', action.postName, 'status' ], 'error' );
					takedown = takedown.setIn( [ 'dmca', action.postName, 'error' ], new Error( ajaxError.xhr.response ) );

					return Observable.of( TakedownActions.update( takedown ) );
				} );
		} );
}

export function saveDmcaUserNotice( action$, store ) {
	return action$.ofType( 'TAKEDOWN_DMCA_USER_NOTICE_SAVE' )
		.flatMap( ( action ) => {
			let notice = action.takedown.dmca.notices.get( action.user.id ) || new Post();

			notice = notice.set( 'text', notice.text || defaultUserNoticeText( action.user.username, action.takedown.dmca.pageIds ) );

			if ( !notice.captcha.id ) {
				notice = notice.set( 'captcha', undefined );
			}

			return Observable.ajax( {
				url: '/api/takedown/' + action.takedown.id + '/user-notice/' + action.user.id,
				method: 'POST',
				body: notice.toJSON(),
				responseType: 'json',
				headers: {
					'Content-Type': 'application/json',
					Authorization: 'Bearer ' + store.getState().token
				}
			} )
				.map( ( ajaxResponse ) => {
					// Get the takedown from the state in case it's been updated while
					// the save was in progress.
					let takedown = store.getState().takedown.list.find( ( item ) => {
						return action.takedown.id === item.id;
					} );

					const response = new Takedown( ajaxResponse.response );

					if ( !takedown ) {
						return {
							type: 'ERROR'
						};
					}

					takedown = takedown.setIn( [ 'dmca', 'userNoticeIds' ], response.dmca.userNoticeIds );

					return TakedownActions.update( takedown );
				} )
				.catch( ( ajaxError ) => {
					// Get the takedown from the state in case it's been updated while
					// the save was in progress.
					let takedown = store.getState().takedown.list.find( ( item ) => {
							return action.takedown.id === item.id;
						} ),
						post = takedown.dmca.notices.get( action.user.id ) || new Post();

					if ( ajaxError.status === 409 && ajaxError.xhr.response.captcha ) {
						post = post.set( 'status', 'captcha' );
						post = post.set( 'captcha', new Captcha( ajaxError.xhr.response.captcha ) );
					} else {
						post = post.set( 'status', 'error' );
						post = post.set( 'error', new Error( ajaxError.xhr.response ) );
					}

					takedown = takedown.setIn( [ 'dmca', 'notices', action.user.id ], post );

					return Observable.of( TakedownActions.update( takedown ) );
				} );
		} );
}
