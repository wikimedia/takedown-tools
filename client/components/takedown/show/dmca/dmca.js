import React from 'react';
import PropTypes from 'prop-types';
import * as moment from 'moment';
import { Set } from 'immutable';
import { Takedown } from 'app/entities/takedown/takedown';
import { Site } from 'app/entities/site';
import { CountrySet } from 'app/entities/country.set';
import { User } from 'app/entities/user';
import { TakedownShowDmcaCommonsPostContainer } from './commons-post.container';
import { TakedownShowDmcaUserNoticeContainer } from './user-notice.container';
import 'fileicon.css/fileicon.css';

export class TakedownShowDmca extends React.Component {
	render() {
		let senderCountry,
			userNotices,
			sent,
			actionTaken,
			originalUrls,
			body,
			files,
			wmfTitle,
			notices,
			lumen;

		if ( this.props.takedown.dmca.senderCountryCode ) {
			senderCountry = CountrySet.find( ( country ) => {
				return this.props.takedown.dmca.senderCountryCode === country.id;
			} );
		}

		if ( this.props.takedown.dmca.sent ) {
			sent = moment.utc( this.props.takedown.dmca.sent ).local().format( 'l' );
		}

		if ( this.props.takedown.dmca.actionTakenId ) {
			actionTaken = this.props.takedown.dmca.actionTakenId.charAt( 0 ).toUpperCase() + this.props.takedown.dmca.actionTakenId.slice( 1 );
		}

		files = this.props.files.map( ( file ) => {
			const ext = file.name.split( '.' ).pop();

			return (
				<a
					key={file.id}
					href={'/api/file/' + file.id + '?token=' + encodeURIComponent( this.props.token )}
					className="d-flex mb-2 flex-row justify-content-start align-items-center"
					download={file.name}
				>
					<span className="file-icon mr-2" data-type={ext}></span>
					<span>{file.name}</span>
				</a>
			);
		} );

		if ( this.props.takedown.dmca.originalUrls ) {
			originalUrls = this.props.takedown.dmca.originalUrls.map( ( url, key ) => {
				return (
					<div key={key}>
						<a href={url}>{url}</a>
					</div>
				);
			} ).toArray();
		}

		if ( this.props.takedown.dmca.body ) {
			body = this.props.takedown.dmca.body.split( '\n' ).map( ( item, key ) => {
				return (
					<span key={key}>
						{item}<br />
					</span>
				);
			} );
		}

		if ( this.props.takedown.dmca.wmfTitle ) {
			wmfTitle = (
				<a href={'https://wikimediafoundation.org/wiki/' + this.props.takedown.dmca.wmfTitle}>{this.props.takedown.dmca.wmfTitle.replace( /_/g, ' ' )}</a>
			);
		}

		if ( this.props.site.id ) {
			notices = this.props.involved.map( ( user ) => {
				return (
					<TakedownShowDmcaUserNoticeContainer key={user.id} user={user} takedown={this.props.takedown} site={this.props.site} />
				);
			} );

			userNotices = (
				<tr>
					<td>
						User Talk Notices
					</td>
					<td>
						{notices}
					</td>
				</tr>
			);
		}

		if ( this.props.takedown.dmca.lumenId ) {
			lumen = (
				<a href={APP_ENV === 'prod' ? `https://lumendatabase.org/notices/${this.props.takedown.dmca.lumenId}` : `https://api-beta.lumendatabase.org/notices/${this.props.takedown.dmca.lumenId}`}>
					{this.props.takedown.dmca.lumenTitle}
				</a>
			);
		}

		return (
			<tbody className="border-top-0">
				<tr>
					<td>Sent</td>
					<td>{sent}</td>
				</tr>
				<tr>
					<td>Action Taken</td>
					<td>{actionTaken}</td>
				</tr>
				<tr>
					<td>Original Urls</td>
					<td>{originalUrls}</td>
				</tr>
				<tr>
					<td>Method</td>
					<td>{this.props.takedown.dmca.method}</td>
				</tr>
				<tr>
					<td>Subject</td>
					<td>{this.props.takedown.dmca.subject}</td>
				</tr>
				<tr>
					<td>Body</td>
					<td>{body}</td>
				</tr>
				<tr>
					<td>Supporting Files</td>
					<td>{files}</td>
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
				<tr>
					<td><a href={APP_ENV === 'prod' ? 'https://lumendatabase.org' : 'https://api-beta.lumendatabase.org'}>Lumen</a></td>
					<td>{lumen}</td>
				</tr>
				<tr>
					<th colSpan="2">Posts</th>
				</tr>
				<tr>
					<td>
						<a href="https://wikimediafoundation.org">Wikimedia Foundation</a>
					</td>
					<td>
						{wmfTitle}
					</td>
				</tr>
				<TakedownShowDmcaCommonsPostContainer postName="commonsPost" takedown={this.props.takedown} />
				<TakedownShowDmcaCommonsPostContainer postName="commonsVillagePumpPost" takedown={this.props.takedown} />
				{userNotices}
			</tbody>
		);
	}
}

TakedownShowDmca.propTypes = {
	takedown: PropTypes.instanceOf( Takedown ),
	site: PropTypes.instanceOf( Site ),
	files: PropTypes.instanceOf( Set ),
	involved: PropTypes.arrayOf( PropTypes.instanceOf( User ) ),
	token: PropTypes.string
};
