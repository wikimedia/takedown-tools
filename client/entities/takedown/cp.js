import { Record, List } from 'immutable';
import { File } from '../file';

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
			files: new List( data.files ? data.files : [] ).map( ( file ) => {
				return new File( file );
			} )
		};
		super( data );
	}
}
