import { Record } from 'immutable';
import { numericHash } from '../utils';

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
