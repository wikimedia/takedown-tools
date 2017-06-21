import React from 'react';
import PropTypes from 'prop-types';

export class Error extends React.Component {
	render() {
		let error;

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

		return (
			<div className="row">
				<div className="col">
					<h2>{error}</h2>
				</div>
			</div>
		);
	}
}

Error.propTypes = {
	code: PropTypes.number
};
