import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';

export class Checkboxes extends React.Component {

	handleInputChange( event ) {
		if ( event.target.checked ) {
			this.props.onChange( this.props.value.add( event.target.value ) );
		} else {
			this.props.onChange( this.props.value.remove( event.target.value ) );
		}
	}

	render() {
		const name = this.props.name ? this.props.name + [] : undefined,
			options = this.props.options.map( ( option ) => {
				const exists = this.props.value.find( ( id ) => {
						return id === option.id;
					} ),
					checked = !!exists;

				return (
					<div key={option.id} className="form-check">
						<label className="form-check-label">
							<input disabled={this.props.disabled} className="form-check-input" type="checkbox" name={name} value={option.id} checked={checked} onChange={this.handleInputChange.bind( this )} /> {option.label}
						</label>
					</div>
				);
			} ).toArray();

		return (
			<div>{options}</div>
		);
	}
}

Checkboxes.propTypes = {
	name: PropTypes.string,
	disabled: PropTypes.bool,
	onChange: PropTypes.func.isRequired,
	value: PropTypes.instanceOf( Set ).isRequired,
	options: PropTypes.instanceOf( Set ).isRequired
};
