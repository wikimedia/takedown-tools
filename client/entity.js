import { Record } from 'immutable';

export class Takedown extends Record( {
	id: undefined,
	reporterId: undefined,
	involvedIds: [],
	// We must send usernames becasue of T168571
	// @link https://phabricator.wikimedia.org/T168571
	involvedNames: [],
	created: undefined,
	status: 'clean',
	error: undefined
}, 'Takedown' ) {

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
			super.hashCode();
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
			super.hashCode();
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
			super.hashCode();
		}

		// @link http://werxltd.com/wp/2010/05/13/javascript-implementation-of-javas-string-hashcode-method/
		let hash = 0, i, chr;
		if ( this.id.length === 0 ) {
			return hash;
		}
		for ( i = 0; i < this.id.length; i++ ) {
			chr = this.id.charCodeAt( i );
			hash = ( ( hash * 31 + chr ) - hash ) + chr;
			hash = hash | 0; // Convert to 32bit integer
		}
		return hash;
	}

}
