export function fetchList( page = 1 ) {
	return {
		type: 'TAKEDOWN_LIST_FETCH',
		page: page
	};
}

export function addMultiple( takedowns ) {
	return {
		type: 'TAKEDOWN_ADD_MULTIPLE',
		takedowns: takedowns
	};
}

export function add( takedown ) {
	return {
		type: 'TAKEDOWN_ADD',
		takedown: takedown
	};
}

export function fetch( id ) {
	return {
		type: 'TAKEDOWN_FETCH',
		id: id
	};
}

export function updateCreate( takedown ) {
	return {
		type: 'TAKEDOWN_CREATE_UPDATE',
		takedown: takedown
	};
}

export function saveCreate() {
	return {
		type: 'TAKEDOWN_CREATE_SAVE'
	};
}

export function clearCreate() {
	return {
		type: 'TAKEDOWN_CREATE_CLEAR'
	};
}

export function incrementPage() {
	return {
		type: 'TAKEDOWN_PAGE_INCREMENT'
	};
}
