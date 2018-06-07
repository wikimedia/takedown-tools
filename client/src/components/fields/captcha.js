import React from 'react';
import PropTypes from 'prop-types';
import { Captcha } from 'app/entities/captcha';
import { Error } from 'app/entities/error';
import { FormGroup } from 'app/components/fields/form-group';

export class CaptchaField extends React.Component {

	render() {
		if ( !this.props.captcha.id ) {
			return null;
		}

		return (
			<FormGroup error={this.props.error} path="captcha" render={( hasError, className ) => (
				<div>
					<label htmlFor="captcha">Captcha</label>
					<img className="d-block mb-3" src={this.props.captcha.url} alt="Captcha Image" />
					<input
						type="text"
						className={className}
						value={this.props.captcha.word || ''}
						onChange={( event ) => this.props.onChange( this.props.captcha.set( 'word', event.target.value ) )}
					/>
				</div>
			)} />
		);
	}
}

CaptchaField.propTypes = {
	captcha: PropTypes.instanceOf( Captcha ).isRequired,
	error: PropTypes.instanceOf( Error ),
	onChange: PropTypes.func.isRequired
};
