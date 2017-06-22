import { Record } from 'immutable';

export const Takedown = Record( {
		id: undefined,
		reporterId: undefined,
		involvedIds: [],
		created: undefined,
		status: 'clean',
		error: undefined
	}, 'Takedown' ),
	User = Record( {
		id: undefined,
		username: undefined,
		status: 'clean',
		error: undefined
	}, 'User' );
