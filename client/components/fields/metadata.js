import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import { MetadataSet } from '../../entity';

export class MetadataField extends React.Component {

	handleInputChange( event ) {
		if ( event.target.checked ) {
			this.props.onChange( this.props.value.add( event.target.value ) );
		} else {
			this.props.onChange( this.props.value.remove( event.target.value ) );
		}
	}

	render() {
		if ( !this.props.type ) {
			return null;
		}

		const options = MetadataSet.filter( ( meta ) => {
			return meta.type === this.props.type;
		} ).map( ( meta ) => {
			const exists = this.props.value.find( ( id ) => {
					return id === meta.id;
				} ),
				checked = !!exists;

			return (
				<div key={meta.id} className="form-check">
					<label className="form-check-label">
						<input className="form-check-input" type="checkbox" name={this.props.name + '[]'} value={meta.id} checked={checked} onChange={this.handleInputChange.bind( this )} /> {meta.label}
					</label>
				</div>
			);
		} ).toArray();

		return (
			<div>{options}</div>
		);
	}
}

MetadataField.propTypes = {
	type: PropTypes.string,
	name: PropTypes.string,
	onChange: PropTypes.func.isRequired,
	value: PropTypes.instanceOf( Set ).isRequired
};
