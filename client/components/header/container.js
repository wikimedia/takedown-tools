import { connect } from 'react-redux';
import { tokenRemove } from '../../actions/token';
import Header from './index';

function parseJwt( token ) {
	const base64Url = token.split( '.' )[ 1 ],
		base64 = base64Url.replace( '-', '+' ).replace( '_', '/' );
	return JSON.parse( window.atob( base64 ) );
}

export default connect(
	( state ) => {
		const token = state.token ? state.token : '';
		let payload = {};

		if ( !token ) {
			return {
				username: ''
			};
		}

		payload = parseJwt( token );

		return {
			username: payload.username
		};
	},
	( dispatch ) => {
		return {
			onLogoutClick: () => {
				dispatch( tokenRemove() );
			}
		};
	}
)( Header );
