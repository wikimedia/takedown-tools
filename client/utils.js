import { User } from './entities/user';

/**
 * Parses the JWT.
 *
 * @param {string} token
 *
 * @return {object}
 */
export function parseJwt( token ) {
	const base64Url = token.split( '.' )[ 1 ],
		base64 = base64Url.replace( '-', '+' ).replace( '_', '/' );
	return JSON.parse( window.atob( base64 ) );
}

/**
 * Converts a JWT to a User.
 *
 * @param {string} token
 *
 * @return {User}
 */
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

/**
 * Generates the Affected Files text.
 *
 * @param {Set} pageIds
 *
 * @return {string}
 */
function getAffectedFilesText( pageIds ) {
	let text = '',
		fileIds;

	if ( !pageIds || pageIds.size === 0 ) {
		return text;
	}

	fileIds = pageIds.filter( ( id ) => {
		return id.startsWith( 'File:' );
	} );

	if ( fileIds.size > 0 ) {
		text = text + '\nAffected file(s):\n';
		fileIds.forEach( ( id ) => {
			text = text + `* {{lf|${id}}}\n`;
		} );
	}

	return text;
}

/**
 * Generates the default Commmons Text.
 *
 * @param {string} title
 * @param {string} wmfTitle
 * @param {Set} pageIds
 *
 * @return {string}
 */
export function defaultCommonsText( title, wmfTitle, pageIds ) {
	if ( !title ) {
		return '';
	}

	let text = `=== ${title} ===\n` +
		'In compliance with the provisions of the US [[:en:Digital Millennium Copyright Act|Digital Millennium Copyright Act]] (DMCA), and at the instruction of the [[Wikimedia Foundation]]\'s legal counsel, ' +
		'one or more files have been deleted from Commons.  Please note that this is an [[Commons:Office actions|official action of the WMF office]] which should not be undone. If you have valid grounds for a counter-claim under the DMCA, please contact me. ' +
		'Please note that this is an [[Commons:Office actions|official action of the WMF office]] which should not be undone. If you have valid grounds for a counter-claim under the DMCA, please contact me.\n';

	if ( wmfTitle ) {
		text = text + `The takedown can be read [[:wmf:${wmfTitle}|'''here''']].\n`;
	}

	text = text + getAffectedFilesText( pageIds );

	text = text + '\nThank you! ~~~~';

	return text;
}

/**
 * Generates the default Commmons Text.
 *
 * @param {string} title
 * @param {string} wmfTitle
 * @param {Set} pageIds
 *
 * @return {string}
 */
export function defaultCommonsVillagePumpText( title, wmfTitle, pageIds ) {
	if ( !title ) {
		return '';
	}

	let text = `=== Notification of DMCA takedown demand - ${title} ===\n` +
		'In compliance with the provisions of the US [[:en:Digital Millennium Copyright Act|Digital Millennium Copyright Act]] (DMCA), and at the instruction of the [[Wikimedia Foundation]]\'s legal counsel, ' +
		'one or more files have been deleted from Commons.  Please note that this is an [[Commons:Office actions|official action of the WMF office]] which should not be undone. If you have valid grounds for a counter-claim under the DMCA, please contact me. ' +
		'Please note that this is an [[Commons:Office actions|official action of the WMF office]] which should not be undone. If you have valid grounds for a counter-claim under the DMCA, please contact me.\n';

	if ( wmfTitle ) {
		text = text + `The takedown can be read [[:wmf:${wmfTitle}|'''here''']].\n`;
	}

	text = text + getAffectedFilesText( pageIds );

	text = text + `\nTo discuss this DMCA takedown, please go to [[COM:DMCA#${title}]] Thank you! ~~~~`;

	return text;
}
