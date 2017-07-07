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

				return (
					<table className="mb-2" key={file.id}>
						<tr>
							<td colSpan="2">
								<div className="d-flex mb-2 flex-row justify-content-start align-items-center">
									<span className="file-icon file-icon mr-2" data-type={ext}></span>
									<span>{file.name}</span>
								</div>
							</td>
						</tr>
						<tr>
							<td>Exif</td>
							<td>
								<pre className="small bg-faded">
									<code>
										{JSON.stringify( file.exif, undefined, 2 )}
									</code>
								</pre>
							</td>
						</tr>
					</table>
				);
			} ).toArray();
		}

		return (
			<tbody className="border-top-0">
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
