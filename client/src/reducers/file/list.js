import { Set } from 'immutable';

export default function list( state = new Set(), action ) {
	switch ( action.type ) {
		case 'FILE_ADD':
			return state.add( action.file ).sortBy( file => file.id );

		case 'FILE_ADD_MULTIPLE':
			return state.union( action.files ).sortBy( file => file.id );

		case 'FILE_UPDATE':
			return state.delete( action.file ).add( action.file );

		case 'FILE_DELETE':
		case 'FILE_REMOVE':
			return state.delete( action.file );

		case 'FILE_SWAP':
			return state.delete( action.oldFile ).add( action.newFile ).sortBy( file => file.id );

		default:
			return state;
	}
}
