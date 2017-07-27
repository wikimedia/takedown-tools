import React from 'react';
import PropTypes from 'prop-types';

export class FormError extends React.Component {

	render() {
		let code;

		if ( !this.props.error || !this.props.error.message ) {
			return null;
		}

		if ( this.props.error.code ) {
			code = (
				<strong>{this.props.error.code}</strong>
			);
		}

		return (
			<div className="alert alert-danger mb-0" role="alert">
				{code} {this.props.error.message}
			</div>
		);
	}
}

FormError.propTypes = {
	error: PropTypes.shape( {
		message: PropTypes.string,
		code: PropTypes.oneOfType( [ PropTypes.number, PropTypes.string ] )
	} )
};
