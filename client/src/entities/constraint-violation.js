import { Record } from 'immutable';

export class ConstraintViolation extends Record( {
	message: undefined,
	code: undefined,
	propertyPath: undefined
}, 'Constraint Violation' ) {}
