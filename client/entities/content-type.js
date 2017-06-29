import { Record } from 'immutable';

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
