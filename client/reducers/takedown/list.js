export default function token( state = [], action ) {
	switch ( action.type ) {
		case 'TAKEDOWN_ADD_MULTIPLE':
			return [
				...state,
				...action.takedowns
			];
		default:
			return state;
	}
}
