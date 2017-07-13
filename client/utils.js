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

	let text = `\n=== ${title} ===\n` +
		'In compliance with the provisions of the US [[:en:Digital Millennium Copyright Act|Digital Millennium Copyright Act]] (DMCA), and at the instruction of the [[Wikimedia Foundation]]\'s legal counsel, ' +
		'one or more files have been deleted from Commons. ' +
		'Please note that this is an [[Commons:Office actions|official action of the WMF office]] which should not be undone. ' +
		'If you have valid grounds for a counter-claim under the DMCA, please contact me.\n';

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

	let text = `\n=== Notification of DMCA takedown demand - ${title} ===\n` +
		'In compliance with the provisions of the US [[:en:Digital Millennium Copyright Act|Digital Millennium Copyright Act]] (DMCA), and at the instruction of the [[Wikimedia Foundation]]\'s legal counsel, ' +
		'one or more files have been deleted from Commons.  ' +
		'Please note that this is an [[Commons:Office actions|official action of the WMF office]] which should not be undone. ' +
		'If you have valid grounds for a counter-claim under the DMCA, please contact me.\n';

	if ( wmfTitle ) {
		text = text + `The takedown can be read [[:wmf:${wmfTitle}|'''here''']].\n`;
	}

	text = text + getAffectedFilesText( pageIds );

	text = text + `\nTo discuss this DMCA takedown, please go to [[COM:DMCA#${title}]] Thank you! ~~~~`;

	return text;
}

/**
 * Generates the default User Notice Text.
 *
 * @param {string} username
 * @param {Set} pageIds
 *
 * @return {string}
 */
export function defaultUserNoticeText( username, pageIds ) {
	const files = pageIds.filter( ( id ) => {
		return id.startsWith( 'File:' );
	} ).map( ( id ) => {
		return `[[:File:${id}]]`;
	} );

	return `\nDear ${username}\n\n` +
		`The Wikimedia Foundation (“Wikimedia”) has taken down content that you posted at ${files.join( ', ' )} due to Wikimedia’s receipt of a validly formulated notice that your posted content was infringing an existing copyright. \n\n` +
	'\'\'\'What Can You Do?\'\'\'\n\n' +
	'You are not obligated to take any action. ' +
	'However, if you feel that your content does not infringe upon any copyrights, you may contest the take down request by submitting a \'counter notice\' to Wikimedia. ' +
	'Before doing so, you should understand your legal position, and you may wish to consult with an attorney.\n\n' +
	'\'\'\'Filing a Counter Notice\'\'\'\n\n' +
	'If you choose to submit a counter notice, you must send a letter asking Wikimedia to restore your content to [mailto:legal@wikimedia.org legal@wikimedia.org], or to our service processor at the following address:  Wikimedia Foundation, c/o CT Corporation System, 818 West Seventh Street, Los Angeles, California, 90017. ' +
	'The letter must comply with DMCA standards, set out in Section (g)(3)(A-D), and must contain the following:\n\n' +
	'* A link to where the content was before we took it down and a description of the material that was removed;\n' +
	'* A statement, under penalty of perjury, that you have a good faith belief that the content was removed or disabled as a result of mistake or misidentification of the material to be removed or disabled;\n' +
	'* Your name, address, and phone number;\n' +
	'* If your address is in the United States, a statement that says “I consent to the jurisdiction of the Federal District Court for the district where my address is located, and I will accept service of process from the person who complained about the content I posted”; alternatively, if your address is outside the United States, a statement that says “I agree to accept service of process in any jurisdiction where the Wikimedia Foundation can be found, and I will accept service of process from the person who complained about the content I posted”; and finally,\n' +
	'* Your physical or electronic signature.\n\n' +
	'Pursuant to the DMCA, Wikimedia must inform the alleged copyright holder that you sent us a counter notice, and give the alleged copyright holder a copy of the counter notice. ' +
	'The alleged copyright holder will then have fourteen (14) business days to file a lawsuit against you to restrain Wikimedia from reposting the content. ' +
	'If Wikimedia does not receive proper notification that the alleged copyright holder has initiated such a lawsuit against you, we will repost your content within ten (10) to fourteen (14) business days.\n\n' +
	'\'\'\'Miscellaneous\'\'\'\n\n' +
	'As a matter of policy and under appropriate circumstances, Wikimedia will block the accounts of repeat infringers as provided by Section 512(i)(1)(A) of the DMCA.\n\n' +
	'If you would like to learn more about Wikimedia’s policies, please refer to the Wikimedia Terms of Use, available at [[wmf:Terms_of_use|Terms of use]], and the Wikimedia Legal Policies, available at [[m:Legal/Legal_Policies]]. ' +
	'More information on DMCA compliance may also be found at:\n\n' +
	'* [https://lumendatabase.org/topics/29 https://lumendatabase.org/topics/29]\n' +
	'* [https://www.eff.org/issues/dmca https://www.eff.org/issues/dmca]\n' +
	'* [http://www.copyright.gov/onlinesp/ http://www.copyright.gov/onlinesp/]\n\n' +
	'Wikimedia appreciates your support. ' +
	'Please do not hesitate to contact us if you have any questions regarding this notice.\n\n\n' +
	'Sincerely,\n' +
	'~~~~';
}
