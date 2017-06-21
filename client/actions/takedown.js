export function fetchTakedownList( page = 1 ) {
	return {
		type: 'TAKEDOWN_LIST_FETCH',
		page: page
	};
}

export function fetchTakedown( id ) {
	return {
		type: 'TAKEDOWN_FETCH',
		id: id
	};
}
