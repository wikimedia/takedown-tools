import { Record } from 'immutable';

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
