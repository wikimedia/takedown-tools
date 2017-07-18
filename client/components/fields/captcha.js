import React from 'react';
import PropTypes from 'prop-types';
import { Captcha } from 'app/entities/captcha';

export class CaptchaField extends React.Component {

	render() {
		if ( !this.props.captcha.id ) {
			return null;
		}

		return (
			<div className="form-group">
				<label htmlFor="captcha">Captcha</label>
				<img className="d-block mb-3" src={'http://via.placeholder.com/350x150' /* this.props.captcha.url */} alt="Captcha Image" />
				<input
					type="text"
					className="form-control"
					value={this.props.captcha.word || ''}
					onChange={( event ) => this.props.onChange( this.props.captcha.set( 'word', event.target.value ) )}
				/>
			</div>
		);
	}
}

CaptchaField.propTypes = {
	captcha: PropTypes.instanceOf( Captcha ).isRequired,
	onChange: PropTypes.func.isRequired
};
