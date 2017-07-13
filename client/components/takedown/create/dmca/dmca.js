import React from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import { Set } from 'immutable';
import moment from 'moment';
import { SelectPages } from 'components/fields/select-pages';
import { ListField } from 'components/fields/list';
import { FileUploadField } from 'components/fields/file-upload';
import { Takedown } from 'entities/takedown/takedown';
import { Site } from 'entities/site';
import { CountrySet } from 'entities/country.set';
import { User } from 'entities/user';
import { DatePicker } from 'components/fields/date-picker';
// import { TakedownCreateDmcaUserNotice } from './user-notice';
// import { defaultCommonsText, defaultCommonsVillagePumpText } from 'utils';

export class TakedownCreateDmca extends React.Component {

	componentWillReceiveProps( nextProps ) {
		let notReady;

		switch ( nextProps.takedown.status ) {
			case 'dirty':
				notReady = nextProps.files.filter( ( file ) => {
					return file.status !== 'ready';
				} );

				if ( notReady.size > 0 ) {
					this.props.updateTakedown( nextProps.takedown.set( 'status', 'pending' ) );
				}
				break;

			case 'pending':
				notReady = nextProps.files.filter( ( file ) => {
					return file.status !== 'ready';
				} );

				if ( notReady.size === 0 ) {
					this.props.updateTakedown( nextProps.takedown.set( 'status', 'dirty' ) );
				}
				break;
		}
	}

	updateField( fieldName, value ) {
		const takedown = this.props.takedown.setIn( [ 'dmca', fieldName ], value )
			.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
	}

	mergeFields( data ) {
		const takedown = this.props.takedown.mergeIn( [ 'dmca' ], data )
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

	addFiles( files ) {
		const fileIds = this.props.takedown.dmca.fileIds.unshift( ...files.map( ( file ) => {
				return file.id;
			} ).toArray() ),
			takedown = this.props.takedown.setIn( [ 'dmca', 'fileIds' ], fileIds )
				.set( 'status', 'dirty' );

		this.props.addFiles( files );
		this.props.updateTakedown( takedown );
	}

	removeFile( file ) {
		let takedown = this.props.takedown,
			fileIds = takedown.dmca.fileIds;

		fileIds = fileIds.remove( fileIds.keyOf( file.id ) );
		takedown = takedown.setIn( [ 'dmca', 'fileIds' ], fileIds )
			.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
		this.props.deleteFile( file );
	}

	// getCommonsTitle() {
	// 	return this.props.takedown.dmca.commonsTitle || this.props.takedown.dmca.wmfTitle || '';
	// }
	//
	// isCommonsTitleReadOnly() {
	// 	return typeof this.props.takedown.dmca.commonsTitle === 'undefined' && this.props.takedown.dmca.wmfTitle;
	// }
	//
	// getCommonsText() {
	// 	if ( typeof this.props.takedown.dmca.commonsText !== 'undefined' ) {
	// 		return this.props.takedown.dmca.commonsText;
	// 	}
	//
	// 	return defaultCommonsText( this.getCommonsTitle(), this.props.takedown.dmca.wmfTitle, this.props.takedown.dmca.pageIds );
	// }
	//
	// getCommonsVillagePumpText() {
	// 	if ( typeof this.props.takedown.dmca.commonsVillagePumpText !== 'undefined' ) {
	// 		return this.props.takedown.dmca.commonsVillagePumpText;
	// 	}
	//
	// 	return defaultCommonsVillagePumpText( this.getCommonsTitle(), this.props.takedown.dmca.wmfTitle, this.props.takedown.dmca.pageIds );
	// }

	render() {
		const countries = CountrySet.map( ( country ) => {
			return {
				value: country.id,
				label: country.name
			};
		} ).toArray();

		let country,
			lumenTitleField,
			wmfText,
			wmfAnnouncement;

		if ( this.props.takedown.dmca.senderCountryCode ) {
			country = countries.find( ( data ) => {
				return this.props.takedown.dmca.senderCountryCode === data.value;
			} );
		}

		if ( this.props.takedown.dmca.lumenSend ) {
			lumenTitleField = (
				<div className="form-group">
					<label htmlFor="lumenTitle">Title</label>
					<input type="text" className="form-control" name="lumenTitle" value={this.props.takedown.dmca.lumenTitle || ''} onChange={this.handleChange.bind( this )} />
				</div>
			);
		}

		if ( this.props.takedown.dmca.wmfTitle && this.props.takedown.dmca.body ) {
			wmfText = (
				<div className="form-group">
					<label>Announcement</label> <small className="text-muted">post the below text to <a target="_blank" rel="noopener noreferrer" href={'https://www.wikimediafoundation.org/wiki/DMCA_' + this.props.takedown.dmca.wmfTitle.replace( / /g, '_' ) + '?action=edit' }>{'DMCA ' + this.props.takedown.dmca.wmfTitle.replace( /_/g, ' ' )}</a></small>
					<textarea className="form-control" readOnly rows="5" value={
						'<div class="mw-code" style="white-space: pre; word-wrap: break-word;"><nowiki>\n' +
						this.props.takedown.dmca.body +
						`\n</nowiki></div>\n[[Category:DMCA ${moment().format( 'Y' )}]]`
					} />
				</div>
			);
		}

		if ( this.props.takedown.dmca.wmfSend ) {
			wmfAnnouncement = (
				<div>
					<div className="form-group">
						<label>Title</label>
						<div className="input-group">
							<span className="input-group-addon">DMCA</span>
							<input type="text" disabled={this.props.disabled} className="form-control" name="wmfTitle" value={this.props.takedown.dmca.wmfTitle || ''} onChange={this.handleChange.bind( this )} />
						</div>
					</div>
					{wmfText}
				</div>
			);
		}

		// if ( this.props.site.projectId === 'commons' ) {
		//
		// 	if ( this.props.takedown.dmca.commonsSend ) {
		//
		// 		if ( this.isCommonsTitleReadOnly() ) {
		// 			commonsNoticeTitleEdit = (
		// 				<span className="input-group-btn">
		// 					<button className="btn btn-secondary" type="button" onClick={() => {
		// 						this.updateField( 'commonsTitle', this.props.takedown.dmca.wmfTitle || '' );
		// 						this.commonsTitleInput.focus();
		// 					}}><i className="material-icons">edit</i></button>
		// 				</span>
		// 			);
		// 		}
		//
		// 		if ( typeof this.props.takedown.dmca.commonsText === 'undefined' ) {
		// 			commonsNoticeTextEdit = (
		// 				<span className="input-group-btn">
		// 					<button className="btn btn-secondary" type="button" onClick={() => {
		// 						this.updateField( 'commonsText', defaultCommonsText( this.getCommonsTitle(), this.props.takedown.dmca.wmfTitle, this.props.takedown.dmca.pageIds ) );
		// 						this.commonsTitleInput.focus();
		// 					}}><i className="material-icons">edit</i></button>
		// 				</span>
		// 			);
		// 		}
		//
		// 		if ( this.props.takedown.dmca.commonsVillagePumpSend ) {
		//
		// 			if ( typeof this.props.takedown.dmca.commonsVillagePumpText === 'undefined' ) {
		// 				commonsVillagePumpTextEdit = (
		// 					<span className="input-group-btn">
		// 						<button className="btn btn-secondary" type="button" onClick={() => {
		// 							this.updateField( 'commonsVillagePumpText', defaultCommonsVillagePumpText( this.getCommonsTitle(), this.props.takedown.dmca.wmfTitle, this.props.takedown.dmca.pageIds ) );
		// 							this.commonsVilagePumpTextInput.focus();
		// 						}}><i className="material-icons">edit</i></button>
		// 					</span>
		// 				);
		// 			}
		//
		// 			commonsVillagePumpForm = (
		// 				<div className="form-group">
		// 					<label>Text</label>
		// 					<div className="input-group">
		// 						<textarea
		// 							className="form-control"
		// 							name="commonsVillagePumpText"
		// 							rows="5"
		// 							ref={( element ) => { this.commonsVillagePumpTextInput = element; }}
		// 							value={this.getCommonsVillagePumpText()}
		// 							readOnly={typeof this.props.takedown.dmca.commonsVillagePumpText === 'undefined'}
		// 							onChange={this.handleChange.bind( this )} />
		// 						{commonsVillagePumpTextEdit}
		// 					</div>
		// 				</div>
		// 			);
		// 		}
		//
		// 		if ( this.props.takedown.dmca.commonsTitle || this.props.takedown.dmca.wmfTitle ) {
		// 			commonsVillagePumpSend = (
		// 				<div>
		// 					<div className="form-group">
		// 						<div className="form-check">
		// 							<label className="form-check-label">
		// 								<input
		// 									disabled={this.props.disabled}
		// 									className="form-check-input"
		// 									type="checkbox"
		// 									name="commonsVillagePumpSend"
		// 									value="commonsVillagePumpSend"
		// 									checked={!!this.props.takedown.dmca.commonsVillagePumpSend}
		// 									onChange={ ( event ) => {
		// 										if ( !event.target.checked ) {
		// 											this.mergeFields( {
		// 												commonsVillagePumpSend: event.target.checked,
		// 												commonsVillagePumpText: undefined
		// 											} );
		// 										} else {
		// 											this.updateField( 'commonsVillagePumpSend', event.target.checked );
		// 										}
		// 									} }
		// 								/> Post to Commons Village Pump
		// 							</label>
		// 						</div>
		// 					</div>
		// 					{commonsVillagePumpForm}
		// 				</div>
		// 			);
		// 		}
		//
		// 		commonsForm = (
		// 			<div>
		// 				<div className="form-group">
		// 					<label>Title</label>
		// 					<div className="input-group">
		// 						<input
		// 							type="text"
		// 							className="form-control"
		// 							name="commonsTitle"
		// 							ref={( element ) => { this.commonsTitleInput = element; }}
		// 							value={this.getCommonsTitle()}
		// 							readOnly={this.isCommonsTitleReadOnly()}
		// 							onChange={this.handleChange.bind( this )} />
		// 						{commonsNoticeTitleEdit}
		// 					</div>
		// 				</div>
		// 				<div className="form-group">
		// 					<label>Text</label>
		// 					<div className="input-group">
		// 						<textarea
		// 							className="form-control"
		// 							name="commonsText"
		// 							rows="5"
		// 							ref={( element ) => { this.commonsTextInput = element; }}
		// 							value={this.getCommonsText()}
		// 							readOnly={typeof this.props.takedown.dmca.commonsText === 'undefined'}
		// 							onChange={this.handleChange.bind( this )} />
		// 						{commonsNoticeTextEdit}
		// 					</div>
		// 				</div>
		// 				{commonsVillagePumpSend}
		// 			</div>
		// 		);
		// 	}
		//
		// 	commonsNotice = (
		// 		<fieldset className="form-gorup">
		// 			<legend>Commons</legend>
		// 			<div className="form-group">
		// 				<div className="form-check">
		// 					<label className="form-check-label">
		// 						<input
		// 							disabled={this.props.disabled}
		// 							className="form-check-input"
		// 							type="checkbox"
		// 							name="commonsSend"
		// 							value="commonsSend"
		// 							checked={!!this.props.takedown.dmca.commonsSend}
		// 							onChange={ ( event ) => {
		// 								if ( !event.target.checked ) {
		// 									this.mergeFields( {
		// 										commonsSend: event.target.checked,
		// 										commonsTitle: undefined,
		// 										commonsText: undefined,
		// 										commonsVillagePumpSend: undefined,
		// 										commonsVillagePumpText: undefined
		// 									} );
		// 								} else {
		// 									this.updateField( 'commonsSend', event.target.checked );
		// 								}
		// 							} }
		// 						/> Post to Commons
		// 					</label>
		// 				</div>
		// 			</div>
		// 			{commonsForm}
		// 		</fieldset>
		// 	);
		// }

		// if ( this.props.involved.length > 0 && this.props.takedown.siteId ) {
		// 	noticeUsers = this.props.involved.map( ( user ) => {
		// 		const noticeUser = this.props.takedown.dmca.userNotices.find( ( item ) => {
		// 			return item.id === user.id;
		// 		} );
		//
		// 		return (
		// 			<TakedownCreateDmcaUserNotice
		// 				key={user.id}
		// 				user={user}
		// 				noticeUser={noticeUser}
		// 				notices={this.props.takedown.dmca.userNotices}
		// 				pageIds={this.props.takedown.dmca.pageIds}
		// 				disabled={this.props.disabled}
		// 				onChange={( notices ) => this.updateField( 'userNotices', notices )} />
		// 		);
		// 	} );
		//
		// 	userNoticeForm = (
		// 		<fieldset className="form-group">
		// 			<legend>User Notices</legend>
		// 			{noticeUsers}
		// 		</fieldset>
		// 	);
		// }

		return (
			<div>
				<div className="form-group">
					<label>Sent</label> <small className="text-muted">date the takedown was sent</small>
					<DatePicker disabled={this.props.disabled} value={this.props.takedown.dmca.sent} onChange={( value ) => this.updateField( 'sent', value )} />
				</div>
				<div className="form-group">
					<label htmlFor="actionTakenId">Action Taken</label>
					<select disabled={this.props.disabled} className="form-control" name="actionTakenId" value={this.props.takedown.dmca.actionTakenId || 'no'} onChange={this.handleChange.bind( this )}>
						<option value="yes">Yes</option>
						<option value="no">No</option>
						<option value="partial">Partial</option>
					</select>
				</div>
				<div className="form-group">
					<label htmlFor="pageIds">Affected Pages</label>
					<SelectPages disabled={this.props.disabled} site={this.props.site} name="pageIds" value={this.props.takedown.dmca.pageIds} onChange={ ( pageIds ) => this.updateField( 'pageIds', pageIds ) } />
				</div>
				<div className="form-group">
					<label htmlFor="originalUrls">Original URLs</label> <small className="text-muted">location of original work</small>
					<ListField disabled={this.props.disabled} required={true} type="url" name="originalUrls" value={this.props.takedown.dmca.originalUrls} onChange={ ( originalUrls ) => this.updateField( 'originalUrls', originalUrls ) } />
				</div>
				<div className="form-group">
					<label htmlFor="method">Method</label> <small className="text-muted">how was the C&D sent? (e.g. email, postal mail, fax ...)</small>
					<input type="text" className="form-control" disabled={this.props.disabled} name="method" value={this.props.takedown.dmca.method || ''} onChange={this.handleChange.bind( this )} />
				</div>
				<div className="form-group">
					<label htmlFor="subject">Subject</label> <small className="text-muted">of the email or fax received</small>
					<input type="text" className="form-control" disabled={this.props.disabled} name="subject" value={this.props.takedown.dmca.subject || ''} onChange={this.handleChange.bind( this )} />
				</div>
				<div className="form-group">
					<label htmlFor="body">Body</label> <small className="text-muted">copy and paste email etc.</small>
					<textarea className="form-control" rows="5" disabled={this.props.disabled} name="body" value={this.props.takedown.dmca.body || ''} onChange={this.handleChange.bind( this )} />
				</div>
				<div className="form-group">
					<label>Supporting Files</label> <small className="text-muted">scanned takedown etc.</small>
					<FileUploadField value={this.props.files} onAddFiles={this.addFiles.bind( this )} onRemoveFile={this.removeFile.bind( this )} />
				</div>
				<fieldset className="form-group">
					<legend>Sender</legend>
					<div className="form-group">
						<label>Name</label> <small className="text-muted">person or organization</small>
						<input disabled={this.props.disabled} type="text" className="form-control" name="senderName" value={this.props.takedown.dmca.senderName || ''} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Name</label> <small className="text-muted">attorney or individual signing</small>
						<input disabled={this.props.disabled} type="text" className="form-control" name="senderPerson" value={this.props.takedown.dmca.senderPerson || ''} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Law Firm or Agent</label> <small className="text-muted">if any</small>
						<input disabled={this.props.disabled} type="text" className="form-control" name="senderFirm" value={this.props.takedown.dmca.senderFirm || ''} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Address</label>
						<input disabled={this.props.disabled} type="text" className="form-control" name="senderAddress[0]" value={this.props.takedown.dmca.senderAddress.get( 0, '' )} onChange={this.handleListChange.bind( this )} />
						<input disabled={this.props.disabled} type="text" className="form-control" name="senderAddress[1]" value={this.props.takedown.dmca.senderAddress.get( 1, '' )} onChange={this.handleListChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>City</label>
						<input disabled={this.props.disabled} type="text" className="form-control" name="senderCity" value={this.props.takedown.dmca.senderCity || ''} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>State / Providence</label>
						<input disabled={this.props.disabled} type="text" className="form-control" name="senderState" value={this.props.takedown.dmca.senderState || ''} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Zip / Postal Code</label>
						<input disabled={this.props.disabled} type="text" className="form-control" name="senderZip" value={this.props.takedown.dmca.senderZip || ''} onChange={this.handleChange.bind( this )} />
					</div>
					<div className="form-group">
						<label>Country</label>
						<Select disabled={this.props.disabled} name="senderCountryCode" options={countries} value={country} onChange={( data ) => this.updateField( 'senderCountryCode', data ? data.value : undefined )} />
					</div>
				</fieldset>
				<fieldset className="form-group">
					<legend>Lumen</legend>
					<div className="form-group">
						<div className="form-check">
							<label className="form-check-label">
								<input
									disabled={this.props.disabled}
									className="form-check-input"
									type="checkbox"
									name="lumenSend"
									value="lumenSend"
									checked={!!this.props.takedown.dmca.lumenSend}
									onChange={ ( event ) => {
										if ( !event.target.checked ) {
											this.mergeFields( {
												lumenSend: event.target.checked,
												lumenTitle: undefined
											} );
										} else {
											this.updateField( 'lumenSend', event.target.checked );
										}
									} }
								/> Send to Lumen
							</label>
						</div>
					</div>
					{lumenTitleField}
				</fieldset>
				<fieldset className="form-gorup">
					<legend>Wikimedia Foundation</legend>
					<div className="form-group">
						<div className="form-check">
							<label className="form-check-label">
								<input
									disabled={this.props.disabled}
									className="form-check-input"
									type="checkbox"
									name="wmfSend"
									value="wmfSend"
									checked={!!this.props.takedown.dmca.wmfSend}
									onChange={ ( event ) => {
										if ( !event.target.checked ) {
											this.mergeFields( {
												wmfSend: event.target.checked,
												wmfTitle: undefined
											} );
										} else {
											this.updateField( 'wmfSend', event.target.checked );
										}
									} }
								/> Post to Wikimedia Foundation
							</label>
						</div>
					</div>
					{wmfAnnouncement}
				</fieldset>
			</div>
		);
	}
}

TakedownCreateDmca.propTypes = {
	updateTakedown: PropTypes.func.isRequired,
	addFiles: PropTypes.func.isRequired,
	deleteFile: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired,
	site: PropTypes.instanceOf( Site ),
	involved: PropTypes.arrayOf( PropTypes.instanceOf( User ) ).isRequired,
	files: PropTypes.instanceOf( Set ).isRequired,
	disabled: PropTypes.bool
};
