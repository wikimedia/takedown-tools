export default function status( state = 'ready', action ) {
	switch ( action.type ) {
		case 'TAKEDOWN_ADD':
		case 'TAKEDOWN_STATUS_READY':
			return 'ready';

		case 'TAKEDOWN_STATUS_FETCHING':
		case 'TAKEDOWN_LIST_FETCH':
		case 'TAKEDOWN_FETCH':
			return 'fetching';

		case 'TAKEDOWN_STATUS_DONE':
			return 'done';

		case 'TAKEDOWN_ADD_MULTIPLE':
			if ( action.takedowns.length < 5 ) {
				return 'done';
			}

			return 'ready';

		default:
			return state;
	}
}
