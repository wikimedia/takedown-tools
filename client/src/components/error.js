import React from 'react';
import PropTypes from 'prop-types';
import { Error } from 'app/entities/error';

export class ErrorComponent extends React.Component {
	render() {
		let error,
			code,
			message;

		switch ( this.props.code ) {
			case 401:
				error = '401 Unauthorized';
				break;

			case 403:
				error = '403 Forbidden';
				break;

			case 404:
				error = '404 Not Found';
				break;

			default:
				error = 'Error';
				break;
		}

		if ( this.props.error ) {
			if ( this.props.error.code ) {
				code = (
					<strong>{this.props.error.code}</strong>
				);
			}

			if ( this.props.error.message ) {
				message = (
					<div className="row">
						<div className="col">
							<div className="alert alert-danger mb-0" role="alert">
								{code} {this.props.error.message}
							</div>
						</div>
					</div>
				);
			}
		}

		return (
			<div>
				<div className="row">
					<div className="col">
						<h2>{error}</h2>
					</div>
				</div>
				{message}
			</div>
		);
	}
}

ErrorComponent.propTypes = {
	code: PropTypes.number,
	error: PropTypes.instanceOf( Error )
};
