import { Record } from 'immutable';

export class Cp extends Record( {
	approved: false,
	approverName: undefined,
	approverId: undefined,
	deniedApprovalReason: undefined,
	accessed: undefined,
	comments: undefined
} ) {}
