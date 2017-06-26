import { Set } from 'immutable';

export default function list( state = new Set(), action ) {
	switch ( action.type ) {
		case 'SITE_ADD':
			return state.add( action.site ).sortBy( site => site.id );

		case 'SITE_ADD_MULTIPLE':
			return state.union( action.sites ).sortBy( site => site.id );

		default:
			return state;
	}
}
