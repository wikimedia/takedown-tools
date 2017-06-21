import { Record } from 'immutable';

export const Takedown = Record( {
		id: null,
		error: null,
		reporterId: null,
		involvedIds: [],
		created: null
	}, 'Takedown' ),

	User = Record( {
		id: null,
		error: null,
		username: null
	}, 'User' );
