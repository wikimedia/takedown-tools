import { Record } from 'immutable';

export class File extends Record( {
	id: undefined,
	name: undefined,
	status: 'ready',
	error: undefined,
	progress: undefined,
	file: undefined,
	uploaded: undefined,
	ip: undefined,
	exif: undefined
} ) {

	equals( other ) {
		if ( !( other instanceof File ) ) {
			return super.equals( other );
		}

		if ( !other.id || !this.id ) {
			return super.equals( other );
		}

		return ( other.id === this.id );
	}
}
