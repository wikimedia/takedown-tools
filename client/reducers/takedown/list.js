export default function token( state = [], action ) {
	let takedowns = [];

	switch ( action.type ) {
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
			].sort( ( a, b ) => {
				if ( a.id < b.id ) {
					return -1;
				}
				if ( a.id > b.id ) {
					return 1;
				}

				return 0;
			} );
		default:
			return state;
	}
}
