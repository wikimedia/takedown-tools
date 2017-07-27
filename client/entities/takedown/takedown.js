import { Record, Set } from 'immutable';
import { Dmca } from './dmca/dmca';
import { Cp } from './cp';

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
	pageIds: new Set(),
	metadataIds: new Set(),
	dmca: new Dmca(),
	cp: new Cp()
}, 'Takedown' ) {

	constructor( data = {} ) {
		data = {
			...data,
			pageIds: new Set( data.pageIds || [] ),
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
