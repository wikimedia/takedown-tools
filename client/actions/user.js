export function addMultiple( users ) {
	return {
		type: 'USER_ADD_MULTIPLE',
		users: users
	};
}

export function add( user ) {
	return {
		type: 'USER_ADD',
		user: user
	};
}

export function update( user ) {
	return {
		type: 'USER_UPDATE',
		user: user
	};
}
