export function parseJwt( token ) {
	const base64Url = token.split( '.' )[ 1 ],
		base64 = base64Url.replace( '-', '+' ).replace( '_', '/' );
	return JSON.parse( window.atob( base64 ) );
}
