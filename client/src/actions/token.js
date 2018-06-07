export function tokenSet( token ) {
	return {
		type: 'TOKEN_SET',
		token: token
	};
}

export function tokenRemove() {
	return {
		type: 'TOKEN_REMOVE'
	};
}
