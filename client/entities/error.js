import { Record, Set } from 'immutable';

export class Error extends Record( {
	message: undefined,
	code: undefined,
	constraintViolations: new Set()
}, 'Error' ) {
	constructor( data = {} ) {
		data = {
			...data,
			constraintViolations: new Set( data.constraintViolations || [] )
		};
		super( data );
	}
}
