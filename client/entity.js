import { Record } from 'immutable';

export const Takedown = Record( {
		id: undefined,
		reporterId: undefined,
		involvedIds: [],
		// We must send usernames becasue of T168571
		// @link https://phabricator.wikimedia.org/T168571
		involvedNames: [],
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
