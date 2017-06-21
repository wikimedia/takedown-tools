export default function token( state = '', action ) {
	switch ( action.type ) {
		case 'TOKEN_ADD':
			return action.token;
		case 'TOKEN_SET':
			window.localStorage.setItem( 'token', action.token );
			return action.token;
		case 'TOKEN_REMOVE':
			window.localStorage.removeItem( 'token' );
			return '';
		default:
			return state;
	}
}
