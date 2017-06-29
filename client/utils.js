import { User } from './entities/user';

export function parseJwt( token ) {
	const base64Url = token.split( '.' )[ 1 ],
		base64 = base64Url.replace( '-', '+' ).replace( '_', '/' );
	return JSON.parse( window.atob( base64 ) );
}

export function getUserFromJwt( token ) {
	return new User( parseJwt( token ) );
}

/**
 * Gets a numeric hash from a string id.
 *
 * @link http://werxltd.com/wp/2010/05/13/javascript-implementation-of-javas-string-hashcode-method/
 *
 * @param {string} id
 *
 * @return {number}
 */
export function numericHash( id ) {
	let hash = 0, i, chr;
	if ( id.length === 0 ) {
		return hash;
	}
	for ( i = 0; i < id.length; i++ ) {
		chr = id.charCodeAt( i );
		hash = ( ( hash * 31 + chr ) - hash ) + chr;
		hash = hash | 0; // Convert to 32bit integer
	}
	return hash;
}
