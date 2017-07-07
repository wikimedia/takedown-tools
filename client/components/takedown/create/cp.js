import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import { FileField } from '../../fields/file';
import { SelectUsers } from '../../fields/select-users';
import { DatePicker } from '../../fields/date-picker';
import { Takedown } from '../../../entities/takedown/takedown';
import { User } from '../../../entities/user';
import 'fileicon.css/fileicon.css';

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

	updateApprover( approver ) {
		const takedown = this.props.takedown
			.setIn( [ 'cp', 'approverName' ], approver.username )
			.setIn( [ 'cp', 'approverId' ], approver.id )
			.set( 'status', 'dirty' );

		this.props.addUsers( [ approver ] );
		this.props.updateTakedown( takedown );
	}

	addFiles( files ) {
		files = this.props.takedown.cp.files.unshift( ...files );

		const takedown = this.props.takedown.setIn( [ 'cp', 'files' ], files )
			.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
	}

	render() {
		let approverReasonField,
			files;

		if ( this.props.takedown.cp.approved ) {
			approverReasonField = (
				<div className="form-group">
					<label htmlFor="approver">Approver</label>
					<SelectUsers
						disabled={this.props.disabled}
						name="approver"
						multi={false}
						value={this.props.approver}
						users={ this.props.users.toArray() }
						onChange={this.updateApprover.bind( this )}
					/>
				</div>
			);
		} else {
			approverReasonField = (
				<div className="form-group">
					<label htmlFor="deniedApprovalReason">Denied Approval Reason</label>
					<input className="form-control" type="text" name="deniedApprovalReason" value={this.props.takedown.cp.deniedApprovalReason || ''} onChange={this.handleChange.bind( this )} />
				</div>
			);
		}

		if ( this.props.takedown.cp.files.size > 0 ) {
			files = this.props.takedown.cp.files.map( ( file ) => {
				const ext = file.name.split( '.' ).pop();

				let exif;

				if ( file.exif ) {
					exif = (
						<div className="form-group">
							<label>Exif Data</label>
							<div className="form-control-static text-muted">
								<pre className="small bg-faded pt-2 pb-2 pl-2 pr-2">
									<code>
										{JSON.stringify( file.exif, undefined, 2 )}
									</code>
								</pre>
							</div>
						</div>
					);
				}

				return (
					<div className="form-control mb-2" key={file.id}>
						<div className="row">
							<div className="col-1">
								<div className="file-icon file-icon-lg" data-type={ext}></div>
							</div>
							<div className="col-10">
								<div className="form-group">
									<div className="form-control-static">{file.name}</div>
								</div>
								{exif}
							</div>
						</div>
					</div>
				);
			} ).toArray();
		}

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
								onChange={ ( event ) => {
									if ( event.target.checked ) {
										this.mergeFields( {
											approved: event.target.checked,
											deniedApprovalReason: undefined
										} );
									} else {
										this.mergeFields( {
											approved: event.target.checked,
											approverName: undefined,
											approverId: undefined
										} );
									}
								}}
							/> Was this release to NCMEC Approved by the legal department?
						</label>
					</div>
				</div>
				{approverReasonField}
				<div className="form-group">
					<label>Accessed</label> <small className="text-muted">when did you you access the content?</small>
					<DatePicker time={true} disabled={this.props.disabled} value={this.props.takedown.cp.accessed} onChange={( value ) => this.updateField( 'accessed', value )} />
				</div>
				<div className="form-group">
					<label htmlFor="comments">Additional Information</label> <small className="text-muted">cu data, other info we may have etc</small>
					<textarea className="form-control" rows="5" disabled={this.props.disabled} name="comments" value={this.props.takedown.cp.comments || ''} onChange={this.handleChange.bind( this )} />
				</div>
				<div className="form-group">
					<label>Files</label>
					<FileField onAddFiles={this.addFiles.bind( this )} />
					{files}
				</div>
			</div>
		);
	}
}

TakedownCreateCp.propTypes = {
	updateTakedown: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired,
	approver: PropTypes.instanceOf( User ),
	users: PropTypes.instanceOf( Set ),
	disabled: PropTypes.bool,
	addUsers: PropTypes.func.isRequired
};
