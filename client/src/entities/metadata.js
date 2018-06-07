import { Record } from 'immutable';

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
