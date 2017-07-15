import React from 'react';
import PropTypes from 'prop-types';

export class Submit extends React.Component {

	render() {
		let className = 'btn btn-primary',
			disabled = false;

		switch ( this.props.status ) {
			case 'error':
				className = 'btn btn-danger';
				break;
			case 'saving':
				disabled = true;
				break;
			case 'pending':
				disabled = true;
				break;
			case 'clean':
				disabled = true;
				break;
		}

		return (
			<div className="form-group row">
				<div className="col text-right">
					<input type="submit" className={className} disabled={disabled} value={this.props.value} />
				</div>
			</div>
		);
	}
}

Submit.propTypes = {
	status: PropTypes.string,
	value: PropTypes.string
};
