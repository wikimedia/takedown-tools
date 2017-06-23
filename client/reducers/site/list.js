export default function list( state = [], action ) {
	let sites = [],
		index;

	switch ( action.type ) {
		case 'SITE_ADD':
			sites = [
				...state
			];
			index = sites.findIndex( ( element ) => {
				return element.id === action.site.id;
			} );

			if ( index !== -1 ) {
				sites = [
					...state.slice( 0, index ),
					...state.slice( index + 1 )
				];
			}

			return [
				...sites,
				action.site
			].sort( ( a, b ) => {
				return a.id - b.id;
			} );

		case 'SITE_ADD_MULTIPLE':
			return action.sites.reduce( ( state, site ) => {
				return list( state, {
					type: 'SITE_ADD',
					site: site
				} );
			}, state );

		default:
			return state;
	}
}
