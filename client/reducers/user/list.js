import { Set } from 'immutable';
import { parseJwt } from '../../utils';
import { User } from '../../entity';

// @TODO Make the state an immutable collection so we can add our own methods
//      to it?
export default function list( state = new Set(), action ) {
	switch ( action.type ) {
		case 'USER_ADD':
			return state.add( action.user ).sortBy( user => user.id );

		case 'USER_ADD_MULTIPLE':
			return state.union( action.users ).sortBy( user => user.id );

		case 'TOKEN_ADD':
			return list( state, {
				type: 'USER_ADD',
				user: new User( parseJwt( action.token ) )
			} );

		default:
			return state;
	}
}
