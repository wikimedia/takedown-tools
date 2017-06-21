function sortById( a, b ) {
	if ( a.id < b.id ) {
		return -1;
	}
	if ( a.id > b.id ) {
		return 1;
	}

	return 0;
}

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
			].sort( sortById );

		case 'TAKEDOWN_ADD_MULTIPLE':
			takedowns = action.takedowns.filter( ( takedown ) => {
				const found = state.find( ( element ) => {
					return element.id === takedown.id;
				} );

				return !found;
			} );

			return [
				...state,
				...takedowns
			].sort( sortById );

		default:
			return state;
	}
}
