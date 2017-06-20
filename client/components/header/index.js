import React from 'react';
import PropTypes from 'prop-types';

export default class Header extends React.Component {
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
					<h1>LCA Tools</h1>
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
