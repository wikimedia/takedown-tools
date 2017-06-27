import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Takedown, ContentTypeSet } from '../../../entity';
import { Checkboxes } from '../../fields/checkboxes';
import * as TakedownActions from '../../../actions/takedown';

export class TakedownCreateDmca extends React.Component {

	updateField( fieldName, value ) {
		const dmca = this.props.takedown.dmca
				.set( fieldName, value ),
			takedown = this.props.takedown
				.set( 'dmca', dmca )
				.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
	}

	render() {
		const sendCe = !!this.props.takedown.dmca.sendCe;

		return (
			<div>
				<div className="form-group row">
					<div className="col">
						<label>Chilling Effects</label>
						<div className="form-check">
							<label className="form-check-label">
								<input className="form-check-input" type="checkbox" name="sendCe" value="sendCe" checked={sendCe} onChange={ ( event ) => this.updateField( 'sendCe', event.target.checked ) } /> Send to Chilling Effects
							</label>
						</div>
					</div>
				</div>
				<div className="form-group row">
					<div className="col">
						<label>Content Types</label>
						<Checkboxes name="contentTypeIds" options={ContentTypeSet} value={this.props.takedown.dmca.contentTypeIds} onChange={ ( value ) => this.updateField( 'contentTypeIds', value ) } />
					</div>
				</div>
			</div>
		);
	}
}

TakedownCreateDmca.propTypes = {
	updateTakedown: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired
};

export const TakedownCreateDmcaContainer = connect(
	undefined,
	( dispatch ) => {
		return {
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.updateCreate( takedown ) );
			}
		};
	}
)( TakedownCreateDmca );
