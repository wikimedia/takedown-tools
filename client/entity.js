import { Record, Set, List } from 'immutable';

/**
 * Gets a numeric hash from a string id.
 *
 * @link http://werxltd.com/wp/2010/05/13/javascript-implementation-of-javas-string-hashcode-method/
 *
 * @param {string} id
 *
 * @return {number}
 */
function numericHash( id ) {
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

export class ContentType extends Record( {
	id: undefined,
	label: undefined
} ) {

	equals( other ) {
		if ( !( other instanceof ContentType ) ) {
			return super.equals( other );
		}

		if ( typeof other.id === 'undefined' || typeof this.id === 'undefined' ) {
			return super.equals( other );
		}

		return ( other.id === this.id );
	}

	hashCode() {
		if ( this.id === 'undefined' ) {
			super.hashCode();
		}

		return this.id;
	}
}

export class Metadata extends Record( {
	id: undefined,
	label: undefined,
	type: undefined
} ) {

	equals( other ) {
		if ( !( other instanceof Metadata ) ) {
			return super.equals( other );
		}

		if ( typeof other.id === 'undefined' || typeof this.id === 'undefined' ) {
			return super.equals( other );
		}

		return ( other.id === this.id );
	}

	hashCode() {
		if ( typeof this.id === 'undefined' ) {
			super.hashCode();
		}

		return this.id;
	}
}

export class Country extends Record( {
	id: undefined,
	name: undefined
} ) {

	equals( other ) {
		if ( !( other instanceof Country ) ) {
			return super.equals( other );
		}

		if ( !other.id || !this.id ) {
			return super.equals( other );
		}

		return ( other.id === this.id );
	}
}

// Fixed Sets of Data.
export const ContentTypeSet = new Set( [
		new ContentType( {
			id: 'file',
			label: 'File/Image'
		} ),
		new ContentType( {
			id: 'text',
			label: 'Text'
		} )
	] ),
	MetadataSet = new Set( [
		new Metadata( {
			id: 'checkuser',
			label: 'Checkuser data was available and is being included below.',
			type: 'cp'
		} ),
		new Metadata( {
			id: 'email-request',
			label: 'An email was sent to legal@rt.wikimedia.org with the file name asking for it to be deleted.',
			type: 'cp'
		} ),
		new Metadata( {
			id: 'taken-down-apparent',
			label: 'The content was taken down and we have awareness of facts or circumstances from which infringing activity is apparent.',
			type: 'dmca'
		} ),
		new Metadata( {
			id: 'taken-down-dmca',
			label: 'The content was taken down pursuant to a DMCA notice.',
			type: 'dmca'
		} ),
		new Metadata( {
			id: 'taken-down-infringing',
			label: 'The content was taken down and we have actual knowledge that the content was infringing copyright ',
			type: 'dmca'
		} ),
		new Metadata( {
			id: 'taken-down-suppressed',
			label: 'The content was taken down and suppressed.',
			type: 'cp'
		} ),
		new Metadata( {
			id: 'taken-down-user-warned',
			label: 'The content was taken down and the user was clearly warned and discouraged from future violations.',
			type: 'dmca'
		} ),
		new Metadata( {
			id: 'user-locked',
			label: 'The user who uploaded the content has been locked.',
			type: 'cp'
		} )
	] ),
	countries = require( '../data/countries' ).map( ( data ) => {
		return new Country( {
			id: data[ 'alpha-2' ],
			name: data.name
		} );
	} ),
	CountrySet = new Set( countries );

export class Dmca extends Record( {
	sendCe: undefined,
	contentTypeIds: new Set(),
	senderName: undefined,
	senderPerson: undefined,
	senderFirm: undefined,
	senderAddress: new List(),
	senderCity: undefined,
	senderState: undefined,
	senderZip: undefined,
	senderCountryCode: undefined,
	sent: undefined,
	actionTakenId: undefined
} ) {
	constructor( data = {} ) {
		data = {
			...data,
			contentTypeIds: new Set( data.contentTypeIds ? data.contentTypeIds : [] ),
			senderAddress: new List( data.senderAddress ? data.senderAddress : [] )
		};
		super( data );
	}

}

export class Cp extends Record {}

export class Takedown extends Record( {
	id: undefined,
	reporterId: undefined,
	involvedIds: [],
	// We must send usernames becasue of T168571
	// @link https://phabricator.wikimedia.org/T168571
	involvedNames: [],
	created: undefined,
	status: 'clean',
	error: undefined,
	siteId: undefined,
	type: undefined,
	metadataIds: new Set(),
	dmca: new Dmca(),
	cp: new Cp()
}, 'Takedown' ) {

	constructor( data = {} ) {
		data = {
			...data,
			metadataIds: new Set( data.metadataIds ? data.metadataIds : [] ),
			dmca: new Dmca( data.dmca ? data.dmca : {} ),
			cp: new Cp( data.cp ? data.cp : {} )
		};
		super( data );
	}

	equals( other ) {
		if ( !( other instanceof Takedown ) ) {
			return super.equals( other );
		}

		if ( typeof other.id === 'undefined' || typeof this.id === 'undefined' ) {
			return super.equals( other );
		}

		return ( other.id === this.id );
	}

	hashCode() {
		if ( typeof this.id === 'undefined' ) {
			return super.hashCode();
		}

		return this.id;
	}
}

export class User extends Record( {
	id: undefined,
	username: undefined,
	status: 'clean',
	error: undefined
}, 'User' ) {

	equals( other ) {
		if ( !( other instanceof User ) ) {
			return super.equals( other );
		}

		if ( typeof other.id === 'undefined' || typeof this.id === 'undefined' ) {
			return super.equals( other );
		}

		return ( other.id === this.id );
	}

	hashCode() {
		if ( typeof this.id === 'undefined' ) {
			return super.hashCode();
		}

		return this.id;
	}

}

export class Site extends Record( {
	id: undefined,
	name: undefined,
	domain: undefined,
	projectId: undefined
}, 'Site' ) {

	equals( other ) {
		if ( !( other instanceof Site ) ) {
			return super.equals( other );
		}

		if ( typeof other.id === 'undefined' || typeof this.id === 'undefined' ) {
			return super.equals( other );
		}

		return ( other.id === this.id );
	}

	hashCode() {
		if ( typeof this.id === 'undefined' ) {
			return super.hashCode();
		}

		return numericHash( this.id );
	}

}
