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

export function update( takedown ) {
	return {
		type: 'TAKEDOWN_UPDATE',
		takedown: takedown
	};
}

export function deleteTakedown( takedown ) {
	return {
		type: 'TAKEDOWN_DELETE',
		takedown: takedown
	};
}

export function remove( takedown ) {
	return {
		type: 'TAKEDOWN_REMOVE',
		takedown: takedown
	};
}

export function saveDmcaPost( takedown, postName ) {
	return {
		type: 'TAKEDOWN_DMCA_POST_SAVE',
		takedown: takedown,
		postName: postName
	};
}

export function saveDmcaUserNotice( takedown, user ) {
	return {
		type: 'TAKEDOWN_DMCA_USER_NOTICE_SAVE',
		takedown: takedown,
		user: user
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
