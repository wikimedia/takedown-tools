import React from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

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
			<header className="row pt-2 pb-1 justify-content-between">
				<div className="col-4 col-sm-2 col-lg-1">
					<Link to="/">
						<img src="https://legalteam.wikimedia.org/static/images/project-logos/legalteamwiki.png" alt="Legal Team Logo" className="img-fluid" />
					</Link>
				</div>
				<div className="col-8 text-right">
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
