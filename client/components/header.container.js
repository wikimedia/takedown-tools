import { connect } from 'react-redux';
import { Header } from './header';
import { tokenRemove } from '../actions/token';
import { parseJwt } from '../utils';

export const HeaderContainer = connect(
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
