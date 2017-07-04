import React from 'react';
import PropTypes from 'prop-types';
import { Takedown } from '../../../entities/takedown/takedown';

export class TakedownCreateCp extends React.Component {

	updateField( fieldName, value ) {
		const takedown = this.props.takedown.setIn( [ 'cp', fieldName ], value )
			.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
	}

	mergeFields( data ) {
		const takedown = this.props.takedown.mergeIn( [ 'cp' ], data )
			.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
	}

	handleChange( event ) {
		this.updateField( event.target.name, event.target.value );
	}

	render() {

		return (
			<div>
				<div className="form-group">
					<label>Approved</label>
					<div className="form-check">
						<label className="form-check-label">
							<input
								disabled={this.props.disabled}
								className="form-check-input"
								type="checkbox"
								name="approved"
								value="approved"
								checked={!!this.props.takedown.cp.approved}
								onChange={ ( event ) => this.updateField( 'approved', event.target.checked )}
							/> Was this release to NCMEC Approved by the legal department?
						</label>
					</div>
				</div>
			</div>
		);
	}
}

TakedownCreateCp.propTypes = {
	updateTakedown: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired,
	disabled: PropTypes.bool
};
