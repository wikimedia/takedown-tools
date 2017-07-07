import { Record, List, fromJS } from 'immutable';

export class Cp extends Record( {
	approved: false,
	approverName: undefined,
	approverId: undefined,
	deniedApprovalReason: undefined,
	accessed: undefined,
	comments: undefined,
	files: new List()
} ) {
	constructor( data = {} ) {
		data = {
			...data,
			files: fromJS( data.files ? data.files : [] ).toList()
		};
		super( data );
	}
}
