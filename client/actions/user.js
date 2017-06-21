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
