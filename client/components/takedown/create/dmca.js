import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Select from 'react-select';
import { Takedown, ContentTypeSet, CountrySet } from '../../../entity';
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

	handleChange( event ) {
		this.updateField( event.target.name, event.target.value );
	}

	handleListChange( event ) {
		const fieldName = event.target.name.replace( /^(.*)\[(\d)\]$/g, '$1' ),
			pos = event.target.name.replace( /^(.*)\[(\d)\]$/g, '$2' ),
			value = this.props.takedown.dmca.get( fieldName ).set( pos, event.target.value );

		this.updateField( fieldName, value );
	}

	render() {
		const sendCe = !!this.props.takedown.dmca.sendCe,
			countries = CountrySet.map( ( country ) => {
				return {
					value: country.id,
					label: country.name
				};
			} ).toArray();
		let country;

		if ( this.props.takedown.dmca.senderCountryCode ) {
			country = countries.find( ( data ) => {
				return this.props.takedown.dmca.senderCountryCode === data.value;
			} );
		}

		return (
			<div>
				<div className="form-group">
					<label>Chilling Effects</label>
					<div className="form-check">
						<label className="form-check-label">
							<input className="form-check-input" type="checkbox" name="sendCe" value="sendCe" checked={sendCe} onChange={ ( event ) => this.updateField( 'sendCe', event.target.checked ) } /> Send to Chilling Effects
						</label>
					</div>
				</div>
				<div className="form-group">
					<label>Content Types</label>
					<Checkboxes name="contentTypeIds" options={ContentTypeSet} value={this.props.takedown.dmca.contentTypeIds} onChange={ ( value ) => this.updateField( 'contentTypeIds', value ) } />
				</div>
				<fieldset className="form-group">
					<legend>Sender</legend>
					<div className="form-group">
						<label>Name</label> <small id="passwordHelpInline" className="text-muted">person or organization</small>
						<input type="text" className="form-control" name="senderName" value={this.props.takedown.dmca.senderName} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Name</label> <small id="passwordHelpInline" className="text-muted">attorney or individual signing</small>
						<input type="text" className="form-control" name="senderPerson" value={this.props.takedown.dmca.senderPerson} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Law Firm or Agent</label> <small id="passwordHelpInline" className="text-muted">if any</small>
						<input type="text" className="form-control" name="senderFirm" value={this.props.takedown.dmca.senderFirm} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Address</label>
						<input type="text" className="form-control" name="senderAddress[0]" value={this.props.takedown.dmca.senderAddress.get( 0, '' )} onChange={this.handleListChange.bind( this )} />
						<input type="text" className="form-control" name="senderAddress[1]" value={this.props.takedown.dmca.senderAddress.get( 1, '' )} onChange={this.handleListChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>City</label>
						<input type="text" className="form-control" name="senderCity" value={this.props.takedown.dmca.senderCity} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>State / Providence</label>
						<input type="text" className="form-control" name="senderState" value={this.props.takedown.dmca.senderState} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Zip / Postal Code</label>
						<input type="text" className="form-control" name="senderZip" value={this.props.takedown.dmca.senderZip} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Country</label>
						<Select name="senderCountryCode" options={countries} value={country} onChange={( data ) => this.updateField( 'senderCountryCode', data ? data.value : undefined )} />
					</div>
				</fieldset>
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
