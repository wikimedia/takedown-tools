import { Observable, Subject } from 'rxjs';
import 'rxjs/add/operator/map';
import 'rxjs/add/observable/dom/ajax';
import 'rxjs/add/observable/from';
import * as TakedownActions from '../actions/takedown';
import * as FileActions from '../actions/file';
import { File } from '../entities/file';

// @TODO support for deleting files.

export function upload( action$, store ) {
	return action$
		.filter( ( action ) => {
			const types = [
				'FILE_ADD',
				'FILE_ADD_MULTIPLE'
			];

			return types.includes( action.type );
		} )
		.flatMap( ( action ) => {
			let files = [];

			switch ( action.type ) {
				case 'FILE_ADD':
					files = [ action.file ];
					break;
				case 'FILE_ADD_MULTIPLE':
					files = [ ...action.files ];
					break;
			}

			return Observable.from( files );
		} )
		.filter( ( file ) => file.status === 'local' )
		.flatMap( ( file ) => {
			const progressSubscriber = new Subject(),
				progress = progressSubscriber.map( ( event ) => {
					const percent = parseInt( ( event.loaded / event.total ) * 100 );
					file = file.set( 'progress', percent );
					return FileActions.update( file );
				} ),
				request = Observable.ajax( {
					url: '/api/file/' + encodeURIComponent( file.name ),
					method: 'POST',
					body: file.file,
					progressSubscriber: progressSubscriber,
					responseType: 'json',
					headers: {
						Authorization: 'Bearer ' + store.getState().token,
						'Content-Type': file.file.type
					}
				} )
					.flatMap( ( ajaxResponse ) => {
						const key = store.getState().takedown.create.dmca.fileIds.keyOf( file.id ),
							response = new File( ajaxResponse.response );

						let takedown = store.getState().takedown.create,
							fileIds = takedown.dmca.fileIds;

						if ( typeof key !== 'undefined' ) {
							takedown = takedown.setIn( [ 'dmca', 'fileIds' ], fileIds.set( key, response.id ) );
							return Observable.concat(
								Observable.of( TakedownActions.updateCreate( takedown ) ),
								Observable.of( FileActions.swap( file, response ) )
							);
						}

						return Observable.of( FileActions.swap( file, response ) );
					} )
					.catch( ( ajaxError ) => {
						return Observable.of( FileActions.update( file.set( 'status', 'error' ).set( 'error', ajaxError.status ) ) );
					} )
					.takeUntil( action$.ofType( 'FILE_DELETE' ).filter( ( action ) => action.file.id === file.id ) );

			file = file.set( 'status', 'uploading' );
			return Observable.merge(
				Observable.of( FileActions.update( file ) ),
				progress,
				request
			);
		} );
}
