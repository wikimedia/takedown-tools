import { Record } from 'immutable';

export class User extends Record( {
	id: undefined,
	username: undefined,
	status: 'clean',
	error: undefined,
	notice: undefined
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
