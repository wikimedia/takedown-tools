import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import { MetadataSet } from '../../entities/metadata.set';
import { Checkboxes } from './checkboxes';

export class MetadataField extends React.Component {

	render() {
		if ( !this.props.type ) {
			return null;
		}

		const options = MetadataSet.filter( ( meta ) => {
			return meta.type === this.props.type;
		} );

		return (
			<Checkboxes disabled={this.props.disabled} name={this.props.name} value={this.props.value} options={options} onChange={this.props.onChange} />
		);
	}
}

MetadataField.propTypes = {
	type: PropTypes.string,
	name: PropTypes.string,
	disabled: PropTypes.bool,
	onChange: PropTypes.func.isRequired,
	value: PropTypes.instanceOf( Set ).isRequired
};
