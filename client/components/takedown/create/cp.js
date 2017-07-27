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
			.set( 'status', 'dirty' )
			.set( 'error', undefined );

		this.props.updateTakedown( takedown );
	}

	mergeFields( data ) {
		const takedown = this.props.takedown.mergeIn( [ 'cp' ], data )
			.set( 'status', 'dirty' )
			.set( 'error', undefined );

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

	updateFile( file ) {
		let takedown = this.props.takedown,
			files = takedown.cp.files;

		const key = files.findIndex( ( item ) => {
			return item.id === file.id;
		} );

		if ( key === -1 ) {
			return;
		}

		files = files.set( key, file );
		takedown = takedown.setIn( [ 'cp', 'files' ], files )
			.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
	}

	removeFile( file ) {
		let takedown = this.props.takedown,
			files = takedown.cp.files;

		const key = files.findIndex( ( item ) => {
			return item.id === file.id;
		} );

		if ( key === -1 ) {
			return;
		}

		files = files.remove( key );
		takedown = takedown.setIn( [ 'cp', 'files' ], files )
			.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
	}

	render() {
		let approverReasonField,
			files;

		if ( this.props.takedown.cp.approved ) {
			approverReasonField = (
				<div className="form-group">
					<label className="form-control-label" htmlFor="approver">Approver</label>
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
					<label className="form-control-label" htmlFor="deniedApprovalReason">Denied Approval Reason</label>
					<input className="form-control" type="text" name="deniedApprovalReason" value={this.props.takedown.cp.deniedApprovalReason || ''} onChange={this.handleChange.bind( this )} />
				</div>
			);
		}

		if ( this.props.takedown.cp.files.size > 0 ) {
			files = this.props.takedown.cp.files.map( ( file, index ) => {
				const ext = file.name.split( '.' ).pop();

				let exif,
					removeClasses = [ 'btn', 'btn-outline-danger', 'btn-sm' ];

				if ( file.exif ) {
					exif = (
						<div className="form-group">
							<label className="form-control-label">Exif Data</label>
							<div>
								<pre className="small bg-faded pt-2 pb-2 pl-2 pr-2">
									<code>
										{JSON.stringify( file.exif, undefined, 2 )}
									</code>
								</pre>
							</div>
						</div>
					);
				}

				if ( this.props.disabled ) {
					removeClasses = [
						...removeClasses,
						'disabled'
					];
				}

				return (
					<div className="form-control mb-2" key={file.id}>
						<div className="row pb-2">
							<div className="col-1">
								<div className="file-icon file-icon-lg ml-3 mt-2" data-type={ext}></div>
							</div>
							<div className="col-10">
								<div>
									<div className="form-control-static">{file.name}</div>
								</div>
								<div className="form-group">
									<label className="form-control-label">Uploaded</label> <small className="text-muted">in local time</small>
									<DatePicker time={true} disabled={this.props.disabled} value={file.uploaded} onChange={( value ) => this.updateFile( file.set( 'uploaded', value ) )} />
								</div>
								<div className="form-group">
									<label className="form-control-label" htmlFor={'files[' + index + '][ip]' }>IP Address</label>
									<input type="text" disabled={this.props.disabled} className="form-control" name={'files[' + index + '][ip]' } value={file.ip || ''} onChange={( event ) => this.updateFile( file.set( 'ip', event.target.value ) )} />
								</div>
								{exif}
								<button type="button" className={removeClasses.join( ' ' )} onClick={() => this.removeFile( file )}>Remove</button>
							</div>
						</div>
					</div>
				);
			} ).toArray();
		}

		return (
			<div>
				<div className="form-group">
					<label className="form-control-label">Approved</label>
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
					<label className="form-control-label">Accessed</label> <small className="text-muted">when did you you access the content?</small>
					<DatePicker time={true} disabled={this.props.disabled} value={this.props.takedown.cp.accessed} onChange={( value ) => this.updateField( 'accessed', value )} />
				</div>
				<div className="form-group">
					<label className="form-control-label" htmlFor="comments">Additional Information</label> <small className="text-muted">cu data, other info we may have etc</small>
					<textarea className="form-control" rows="5" disabled={this.props.disabled} name="comments" value={this.props.takedown.cp.comments || ''} onChange={this.handleChange.bind( this )} />
				</div>
				<div className="form-group">
					<label className="form-control-label">Files</label>
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
