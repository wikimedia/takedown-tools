export default function page( state = 0, action ) {
	switch ( action.type ) {
		case 'TAKEDOWN_LIST_FETCH':
		case 'TAKEDOWN_PAGE_INCREMENT':
			return state + 1;
		case 'TAKEDOWN_PAGE_DECREMENT':
			return state - 1;
		default:
			return state;
	}
}
