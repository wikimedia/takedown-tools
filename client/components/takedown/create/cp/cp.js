import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import { FileField } from 'app/components/fields/file';
import { SelectUsers } from 'app/components/fields/select-users';
import { DatePicker } from 'app/components/fields/date-picker';
import { Takedown } from 'app/entities/takedown/takedown';
import { User } from 'app/entities/user';
import { FormGroup } from 'app/components/fields/form-group';
import { removeErrors } from 'app/utils';
import { TakedownCreateCpFile } from './file';
import 'fileicon.css/fileicon.css';

export class TakedownCreateCp extends React.Component {

	updateField( fieldName, value ) {
		let takedown = this.props.takedown.setIn( [ 'cp', fieldName ], value )
			.set( 'status', 'dirty' );

		takedown = removeErrors( takedown, 'cp.' + fieldName );

		this.props.updateTakedown( takedown );
	}

	mergeFields( data ) {
		let takedown = this.props.takedown.mergeIn( [ 'cp' ], data )
			.set( 'status', 'dirty' );

		Object.keys( data ).forEach( ( fieldName ) => {
			takedown = removeErrors( takedown, 'cp.' + fieldName );
		} );

		this.props.updateTakedown( takedown );
	}

	handleChange( event ) {
		this.updateField( event.target.name, event.target.value );
	}

	updateApprover( approver ) {
		let takedown = this.props.takedown
			.setIn( [ 'cp', 'approverName' ], approver.username )
			.setIn( [ 'cp', 'approverId' ], approver.id )
			.set( 'status', 'dirty' );

		takedown = removeErrors( takedown, 'cp.approverName' );

		this.props.addUsers( [ approver ] );
		this.props.updateTakedown( takedown );
	}

	addFiles( files ) {
		files = this.props.takedown.cp.files.unshift( ...files );

		let takedown = this.props.takedown.setIn( [ 'cp', 'files' ], files )
			.set( 'status', 'dirty' );

		takedown = removeErrors( takedown, 'cp.files' );

		this.props.updateTakedown( takedown );
	}

	render() {
		let approverReasonField,
			files;

		if ( this.props.takedown.cp.approved ) {
			approverReasonField = (
				<FormGroup path="cp.approverName" error={this.props.takedown.error} render={() => (
					<div>
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
				)} />
			);
		} else {
			approverReasonField = (
				<FormGroup path="cp.deniedApprovalReason" error={this.props.takedown.error} render={( hasError, className ) => (
					<div>
						<label className="form-control-label" htmlFor="deniedApprovalReason">Denied Approval Reason</label>
						<input className={className} type="text" name="deniedApprovalReason" value={this.props.takedown.cp.deniedApprovalReason || ''} onChange={this.handleChange.bind( this )} />
					</div>
				)} />
			);
		}

		if ( this.props.takedown.cp.files.size > 0 ) {
			files = this.props.takedown.cp.files.map( ( file, index ) => (
				<TakedownCreateCpFile
					takedown={this.props.takedown}
					file={file}
					index={index}
					updateTakedown={this.props.updateTakedown}
					disabled={this.props.disabled}
					key={file.id}
				/>
			) ).toArray();
		}

		return (
			<div>
				<FormGroup path="cp.approved" error={this.props.takedown.error} render={( hasError ) => (
					<div>
						<label className="form-control-label">Approved</label>
						<div className={hasError ? 'form-check has-danger' : 'form-check'}>
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
				)} />
				{approverReasonField}
				<FormGroup path="cp.accessed" error={this.props.takedown.error} render={() => (
					<div>
						<label className="form-control-label">Accessed</label> <small className="text-muted">when did you you access the content?</small>
						<DatePicker time={true} disabled={this.props.disabled} value={this.props.takedown.cp.accessed} onChange={( value ) => this.updateField( 'accessed', value )} />
					</div>
				)} />
				<FormGroup path="cp.comments" error={this.props.takedown.error} render={( hasError, className ) => (
					<div>
						<label className="form-control-label" htmlFor="comments">Additional Information</label> <small className="text-muted">cu data, other info we may have etc</small>
						<textarea className={className} rows="5" disabled={this.props.disabled} name="comments" value={this.props.takedown.cp.comments || ''} onChange={this.handleChange.bind( this )} />
					</div>
				)} />
				<FormGroup path="cp.files" error={this.props.takedown.error} render={() => (
					<div>
						<label className="form-control-label">Files</label>
						<FileField onAddFiles={this.addFiles.bind( this )} />
						{files}
					</div>
				)} />
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
