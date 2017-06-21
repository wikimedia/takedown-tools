import { parseJwt } from '../../utils';
import { User } from '../../entity';

export default function list( state = [], action ) {
	let users = [],
		user,
		index;

	switch ( action.type ) {
		case 'USER_ADD':
			users = [
				...state
			];
			index = users.findIndex( ( element ) => {
				return element.id === action.user.id;
			} );

			if ( index !== -1 ) {
				users = [
					...state.slice( 0, index ),
					...state.slice( index + 1 )
				];
			}

			return [
				...users,
				action.user
			].sort( ( a, b ) => {
				return a.id - b.id;
			} );

		case 'USER_ADD_MULTIPLE':
			return action.users.reduce( ( state, user ) => {
				return list( state, {
					type: 'USER_ADD',
					user: user
				} );
			}, state );

		case 'TOKEN_ADD':
			user = new User( parseJwt( action.token ) );

			return list( state, {
				type: 'USER_ADD',
				user: user
			} );

		default:
			return state;
	}
}
