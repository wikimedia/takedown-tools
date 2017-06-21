import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Link } from 'react-router-dom';
import { tokenRemove } from '../actions/token';
import { parseJwt } from '../utils';

export class Header extends React.Component {
	render() {
		let menu;

		if ( this.props.username ) {
			menu = (
				<span>{this.props.username} &bull; <a href="#" onClick={this.props.onLogoutClick.bind( this )}>Logout</a></span>
			);
		} else {
			menu = (
				<a href="/login">Login</a>
			);
		}

		return (
			<header className="row">
				<div className="col">
					<h1><Link to="/">LCA Tools</Link></h1>
				</div>
				<div className="col-3 text-right">
					{menu}
				</div>
			</header>
		);
	}
}

Header.propTypes = {
	username: PropTypes.string.isRequired,
	onLogoutClick: PropTypes.func.isRequired
};

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
