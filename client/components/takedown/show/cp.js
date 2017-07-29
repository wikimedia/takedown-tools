import React from 'react';
import PropTypes from 'prop-types';
import * as moment from 'moment';
import { Takedown } from '../../../entities/takedown/takedown';
import 'fileicon.css/fileicon.css';

export class TakedownShowCp extends React.Component {
	render() {
		let accessed,
			comments,
			files;

		if ( this.props.takedown.cp.accessed ) {
			accessed = moment.utc( this.props.takedown.cp.accessed ).local().format( 'l LT' );
		}

		if ( this.props.takedown.cp.comments ) {
			comments = this.props.takedown.cp.comments.split( '\n' ).map( ( item, key ) => {
				return (
					<span key={key}>
						{item}<br />
					</span>
				);
			} );
		}

		if ( this.props.takedown.cp.files.size > 0 ) {
			files = this.props.takedown.cp.files.map( ( file ) => {
				const ext = file.name.split( '.' ).pop();

				let uploaded;

				if ( file.uploaded ) {
					uploaded = moment.utc( file.uploaded ).local().format( 'l LT' );
				}

				return (
					<table className="mb-2" key={file.id}>
						<tbody>
							<tr>
								<td colSpan="2">
									<div className="d-flex mb-2 flex-row justify-content-start align-items-center">
										<span className="file-icon file-icon mr-2" data-type={ext}></span>
										<span>{file.name}</span>
									</div>
								</td>
							</tr>
							<tr>
								<td><a href="http://www.missingkids.com/">NCMEC</a></td>
								<td>{file.ncmecId}</td>
							</tr>
							<tr>
								<td>Uploaded</td>
								<td>{uploaded}</td>
							</tr>
							<tr>
								<td>IP Address</td>
								<td>{file.ip}</td>
							</tr>
							<tr>
								<td>Exif</td>
								<td>
									<pre className="small bg-faded pt-2 pb-2 pl-2 pr-2 mb-0">
										<code>
											{JSON.stringify( file.exif, undefined, 2 )}
										</code>
									</pre>
								</td>
							</tr>
						</tbody>
					</table>
				);
			} ).toArray();
		}

		return (
			<tbody className="border-top-0">
				<tr>
					<td><a href="http://www.missingkids.com/">NCMEC</a></td>
					<td>{this.props.takedown.cp.ncmecId}</td>
				</tr>
				<tr>
					<td>Approved</td>
					<td>{this.props.takedown.cp.approved ? 'Yes' : 'No'}</td>
				</tr>
				<tr>
					<td>Approver</td>
					<td>{this.props.takedown.cp.approverName}</td>
				</tr>
				<tr>
					<td>Denied Approval Reason</td>
					<td>{this.props.takedown.cp.deniedApprovalReason}</td>
				</tr>
				<tr>
					<td>Accessed</td>
					<td>{accessed}</td>
				</tr>
				<tr>
					<td>Additional Information</td>
					<td>{comments}</td>
				</tr>
				<tr>
					<td>Files</td>
					<td>{files}</td>
				</tr>
			</tbody>
		);
	}
}

TakedownShowCp.propTypes = {
	takedown: PropTypes.instanceOf( Takedown )
};
