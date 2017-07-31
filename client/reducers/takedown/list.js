import { Set } from 'immutable';
import * as moment from 'moment';

export default function list( state = new Set(), action ) {
	switch ( action.type ) {
		case 'TAKEDOWN_ADD':
			return state.add( action.takedown ).sort( ( a, b ) => {
				return moment.utc( b.created ).diff( moment.utc( a.created ) );
			} );

		case 'TAKEDOWN_UPDATE':
			return state.delete( action.takedown ).add( action.takedown ).sort( ( a, b ) => {
				return moment.utc( b.created ).diff( moment.utc( a.created ) );
			} );

		case 'TAKEDOWN_DELETE':
			return state.delete( action.takedown );

		case 'TAKEDOWN_DMCA_POST_SAVE':
			return state.delete( action.takedown ).add( action.takedown.setIn( [ 'dmca', action.postName, 'status' ], 'saving' ) ).sort( ( a, b ) => {
				return moment.utc( b.created ).diff( moment.utc( a.created ) );
			} );

		case 'TAKEDOWN_ADD_MULTIPLE':
			return state.union( action.takedowns ).sort( ( a, b ) => {
				return moment.utc( b.created ).diff( moment.utc( a.created ) );
			} );

		default:
			return state;
	}
}
