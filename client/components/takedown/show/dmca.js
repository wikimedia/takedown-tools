import React from 'react';
import PropTypes from 'prop-types';
import * as moment from 'moment';
import { Takedown } from '../../../entities/takedown/takedown';
import { ContentTypeSet } from '../../../entities/content-type.set';
import { CountrySet } from '../../../entities/country.set';

export class TakedownShowDmca extends React.Component {
	render() {
		let contentTypes,
			senderCountry,
			sent,
			actionTaken;

		if ( this.props.takedown.dmca.contentTypeIds.size > 0 ) {
			contentTypes = this.props.takedown.dmca.contentTypeIds.map( ( id ) => {
				return ContentTypeSet.find( ( contentType ) => {
					return id === contentType.id;
				} );
			} ).filter( ( contentType ) => !!contentType )
				.map( ( contentType ) => {
					return (
						<div key={contentType.id}>
							{contentType.label}
						</div>
					);
				} ).toArray();
		}

		if ( this.props.takedown.dmca.senderCountryCode ) {
			senderCountry = CountrySet.find( ( country ) => {
				return this.props.takedown.dmca.senderCountryCode === country.id;
			} );
		}

		if ( this.props.takedown.dmca.sent ) {
			sent = moment.utc( this.props.takedown.dmca.sent ).format( 'l' );
		}

		if ( this.props.takedown.dmca.actionTakenId ) {
			actionTaken = this.props.takedown.dmca.actionTakenId.charAt( 0 ).toUpperCase() + this.props.takedown.dmca.actionTakenId.slice( 1 );
		}

		return (
			<tbody className="border-top-0">
				<tr>
					<td>Sent to Chilling Effects</td>
					<td>{this.props.takedown.dmca.ceSend ? 'Yes' : 'No'}</td>
				</tr>
				<tr>
					<td>Content Types</td>
					<td>{contentTypes}</td>
				</tr>
				<tr>
					<td>Sent</td>
					<td>{sent}</td>
				</tr>
				<tr>
					<td>Action Taken</td>
					<td>{actionTaken}</td>
				</tr>
				<tr>
					<th colSpan="2">Sender</th>
				</tr>
				<tr>
					<td>Name</td>
					<td>{this.props.takedown.dmca.senderName}</td>
				</tr>
				<tr>
					<td>Person</td>
					<td>{this.props.takedown.dmca.senderPerson}</td>
				</tr>
				<tr>
					<td>Law Firm or Agent</td>
					<td>{this.props.takedown.dmca.senderFirm}</td>
				</tr>
				<tr>
					<td>Address</td>
					<td>
						{this.props.takedown.dmca.senderAddress.map( ( line, index ) => {
							return (
								<div key={index}>
									{line}
								</div>
							);
						} ) }
					</td>
				</tr>
				<tr>
					<td>City</td>
					<td>{this.props.takedown.dmca.senderCity}</td>
				</tr>
				<tr>
					<td>State / Providence</td>
					<td>{this.props.takedown.dmca.senderState}</td>
				</tr>
				<tr>
					<td>Zip / Postal Code</td>
					<td>{this.props.takedown.dmca.senderZip}</td>
				</tr>
				<tr>
					<td>Country</td>
					<td>{senderCountry ? senderCountry.name : undefined}</td>
				</tr>
			</tbody>
		);
	}
}

TakedownShowDmca.propTypes = {
	takedown: PropTypes.instanceOf( Takedown )
};
