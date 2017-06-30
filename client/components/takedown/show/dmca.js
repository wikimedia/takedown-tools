import React from 'react';
import PropTypes from 'prop-types';
import * as moment from 'moment';
import { Title } from 'mediawiki-title';
import { Takedown } from '../../../entities/takedown/takedown';
import { Site } from '../../../entities/site';
import { CountrySet } from '../../../entities/country.set';

export class TakedownShowDmca extends React.Component {
	render() {
		let senderCountry,
			sent,
			actionTaken,
			pages;

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

		if ( this.props.takedown.siteId && this.props.takedown.dmca.pageIds && this.props.site.info ) {
			pages = this.props.takedown.dmca.pageIds.map( ( id ) => {
				const url = 'https://' + this.props.site.domain + id.replace( /^(.*)$/, this.props.site.info.general.articlepath ),
					title = Title.newFromText( id, this.props.site.info );

				let content;

				if ( title.getNamespace().isMain() ) {
					content = title.getKey().replace( '_', ' ' );
				} else {
					content = `${title.getKey().replace( '_', ' ' )} (${title.getNamespace().getNormalizedText()})`;
				}

				return (
					<div key={id}>
						<a href={url}>{content}</a>
					</div>
				);
			} );
		}

		return (
			<tbody className="border-top-0">
				<tr>
					<td>Sent to Chilling Effects</td>
					<td>{this.props.takedown.dmca.ceSend ? 'Yes' : 'No'}</td>
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
					<td>Pages </td>
					<td>{pages}</td>
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
	takedown: PropTypes.instanceOf( Takedown ),
	site: PropTypes.instanceOf( Site )
};
