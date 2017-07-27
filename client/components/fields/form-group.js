import React from 'react';
import PropTypes from 'prop-types';
import { Error } from 'app/entities/error';

export class FormGroup extends React.Component {

	render() {
		const standard = (
			<div className="form-group">
				{this.props.render( false, 'form-control' )}
			</div>
		);

		let errors;

		if ( !this.props.error ) {
			return standard;
		}

		if ( this.props.error.constraintViolations.size === 0 ) {
			return standard;
		}

		errors = this.props.error.constraintViolations.filter( ( violation ) => {
			return violation.propertyPath === this.props.path;
		} );

		if ( errors.size === 0 ) {
			return standard;
		}

		return (
			<div className="form-group has-danger">
				{this.props.render( true, 'form-control form-control-danger' )}
				{errors.map( ( error ) => (
					<div className="form-control-feedback" key={error.code}>
						{error.message}
					</div>
				) ).toArray()}
			</div>
		);
	}
}

FormGroup.propTypes = {
	path: PropTypes.string.isRequired,
	render: PropTypes.func.isRequired,
	error: PropTypes.instanceOf( Error )
};
