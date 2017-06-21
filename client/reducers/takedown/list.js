import { sortById } from '../../utils';

export default function list( state = [], action ) {
	let takedowns = [],
		index;

	switch ( action.type ) {
		case 'TAKEDOWN_ADD':
			takedowns = [
				...state
			];
			index = takedowns.findIndex( ( element ) => {
				return element.id === action.takedown.id;
			} );

			if ( index !== -1 ) {
				takedowns = [
					...state.slice( 0, index ),
					...state.slice( index + 1 )
				];
			}

			return [
				...takedowns,
				action.takedown
			].sort( ( a, b ) => {
				return b.created.diff( a.created );
			} );

		case 'TAKEDOWN_ADD_MULTIPLE':
			return action.takedowns.reduce( ( state, takedown ) => {
				return list( state, {
					type: 'TAKEDOWN_ADD',
					takedown: takedown
				} );
			}, state );

		default:
			return state;
	}
}
