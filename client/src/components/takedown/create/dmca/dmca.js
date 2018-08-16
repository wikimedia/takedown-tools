import React from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import { Set } from 'immutable';
import moment from 'moment';
import { ListField } from 'app/components/fields/list';
import { FormGroup } from 'app/components/fields/form-group';
import { FileUploadField } from 'app/components/fields/file-upload';
import { Takedown } from 'app/entities/takedown/takedown';
import { CountrySet } from 'app/entities/country.set';
import { User } from 'app/entities/user';
import { DatePicker } from 'app/components/fields/date-picker';
import { removeErrors } from 'app/utils';

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
		let takedown = this.props.takedown.setIn( [ 'dmca', fieldName ], value )
			.set( 'status', 'dirty' );

		takedown = removeErrors( takedown, 'dmca.' + fieldName );

		this.props.updateTakedown( takedown );
	}

	mergeFields( data ) {
		let takedown = this.props.takedown.mergeIn( [ 'dmca' ], data )
			.set( 'status', 'dirty' );

		Object.keys( data ).forEach( ( fieldName ) => {
			takedown = removeErrors( takedown, 'dmca.' + fieldName );
		} );

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
		} ).toArray() );

		let takedown = this.props.takedown.setIn( [ 'dmca', 'fileIds' ], fileIds )
			.set( 'status', 'dirty' );

		takedown = removeErrors( takedown, 'dmca.files' );

		this.props.addFiles( files );
		this.props.updateTakedown( takedown );
	}

	removeFile( file ) {
		let takedown = this.props.takedown,
			fileIds = takedown.dmca.fileIds;

		fileIds = fileIds.remove( fileIds.keyOf( file.id ) );
		takedown = takedown.setIn( [ 'dmca', 'fileIds' ], fileIds )
			.set( 'status', 'dirty' );

		takedown = removeErrors( takedown, 'dmca.files' );

		this.props.updateTakedown( takedown );
		this.props.deleteFile( file );
	}

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
			attachment,
			wmfAnnouncement;

		if ( this.props.takedown.dmca.senderCountryCode ) {
			country = countries.find( ( data ) => {
				return this.props.takedown.dmca.senderCountryCode === data.value;
			} );
		}

		if ( this.props.takedown.dmca.lumenSend ) {
			lumenTitleField = (
				<FormGroup path="dmca.lumenTitle" error={this.props.takedown.error} render={ ( hasError, className ) => (
					<div>
						<label className="form-control-label" htmlFor="lumenTitle">Title</label>
						<input
							type="text"
							className={className}
							name="lumenTitle"
							value={this.props.takedown.dmca.lumenTitle || ''}
							onChange={this.handleChange.bind( this )}
						/>
					</div>
				) } />
			);
		}

		if ( this.props.takedown.dmca.wmfTitle ) {
			if ( this.props.files.size > 0 ) {
				attachment = this.props.files.map( ( file ) => file.name ).join( ' ' );
			}

			wmfText = (
				<div className="form-group">
					<label className="form-control-label">Announcement</label> <small className="text-muted">post the below text to <a target="_blank" rel="noopener noreferrer" href={'https://foundation.wikimedia.org/wiki/DMCA_' + this.props.takedown.dmca.wmfTitle.replace( / /g, '_' ) + '?action=edit' }>{'DMCA ' + this.props.takedown.dmca.wmfTitle.replace( /_/g, ' ' )}</a></small>
					<textarea className="form-control" readOnly rows="5" value={
						'{{DMCA email\n' +
						`|from=${this.props.takedown.dmca.from || ''}\n` +
						`|date=${moment.utc( this.props.takedown.dmca.sent ).format( 'MMMM D[,] YYYY [at] HH:mm:ss z' )}\n` +
						`|subject=${this.props.takedown.dmca.subject || ''}\n` +
						`|to=${this.props.takedown.dmca.to || ''}\n` +
						`|content=${this.props.contentLink || ''}\n` +
						`|attachment=${attachment || ''}\n` +
						'|message=<nowiki>\n' +
						( this.props.takedown.dmca.body || '' ) + '\n' +
						'</nowiki>\n' +
						`| year = ${moment.utc().format( 'Y' )}}}`
					} />
				</div>
			);
		}

		if ( this.props.takedown.dmca.wmfSend ) {
			wmfAnnouncement = (
				<div>
					<FormGroup path="dmca.wmfTitle" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label">Title</label>
							<div className="input-group">
								<span className="input-group-addon">DMCA</span>
								<input type="text" disabled={this.props.disabled} className={className} name="wmfTitle" value={this.props.takedown.dmca.wmfTitle || ''} onChange={this.handleChange.bind( this )} />
							</div>
						</div>
					) } />
					{wmfText}
				</div>
			);
		}

		return (
			<div>
				<FormGroup path="dmca.actionTaken" error={this.props.takedown.error} render={ ( hasError, className ) => (
					<div>
						<label className="form-control-label" htmlFor="actionTakenId">Action Taken</label>
						<select disabled={this.props.disabled} className={className} name="actionTakenId" value={this.props.takedown.dmca.actionTakenId || 'no'} onChange={this.handleChange.bind( this )}>
							<option value="yes">Yes</option>
							<option value="no">No</option>
							<option value="partial">Partial</option>
						</select>
					</div>
				) } />
				<FormGroup path="dmca.originalUrls" error={this.props.takedown.error} render={ () => (
					<div>
						<label className="form-control-label" htmlFor="originalUrls">Original URLs</label> <small className="text-muted">location of original work</small>
						<ListField disabled={this.props.disabled} required={true} type="url" name="originalUrls" value={this.props.takedown.dmca.originalUrls} onChange={ ( originalUrls ) => this.updateField( 'originalUrls', originalUrls ) } />
					</div>
				) } />
				<fieldset className="form-group">
					<legend>Notice</legend>
					<FormGroup path="dmca.method" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label" htmlFor="method">Method</label> <small className="text-muted">how was the C&D sent? (e.g. email, postal mail, fax ...)</small>
							<input type="text" className={className} disabled={this.props.disabled} name="method" value={this.props.takedown.dmca.method || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.sent" error={this.props.takedown.error} render={ () => (
						<div>
							<label className="form-control-label">Sent</label> <small className="text-muted">date the takedown was sent</small>
							<DatePicker time={true} disabled={this.props.disabled} value={this.props.takedown.dmca.sent} onChange={( value ) => this.updateField( 'sent', value )} />
						</div>
					) } />
					<FormGroup path="dmca.to" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label" htmlFor="to">To</label> <small className="text-muted">emails or numbers the notice was sent to</small>
							<input type="text" className={className} disabled={this.props.disabled} name="to" value={this.props.takedown.dmca.to || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.from" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label" htmlFor="from">From</label> <small className="text-muted">emails or numbers the notice was sent from</small>
							<input type="text" className={className} disabled={this.props.disabled} name="from" value={this.props.takedown.dmca.from || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.subject" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label" htmlFor="subject">Subject</label> <small className="text-muted">of the email or fax received</small>
							<input type="text" className={className} disabled={this.props.disabled} name="subject" value={this.props.takedown.dmca.subject || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.body" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label" htmlFor="body">Body</label> <small className="text-muted">copy and paste email etc.</small>
							<textarea className={className} rows="5" disabled={this.props.disabled} name="body" value={this.props.takedown.dmca.body || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.files" error={this.props.takedown.error} render={ () => (
						<div>
							<label className="form-control-label">Supporting Files</label> <small className="text-muted">scanned takedown etc.</small>
							<FileUploadField value={this.props.files} onAddFiles={this.addFiles.bind( this )} onRemoveFile={this.removeFile.bind( this )} />
						</div>
					) } />
				</fieldset>
				<fieldset className="form-group">
					<legend>Sender</legend>
					<FormGroup path="dmca.senderName" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label">Name</label> <small className="text-muted">person or organization</small>
							<input disabled={this.props.disabled} type="text" className={className} name="senderName" value={this.props.takedown.dmca.senderName || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.senderPerson" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label">Name</label> <small className="text-muted">attorney or individual signing</small>
							<input disabled={this.props.disabled} type="text" className={className} name="senderPerson" value={this.props.takedown.dmca.senderPerson || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.senderFirm" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label">Law Firm or Agent</label> <small className="text-muted">if any</small>
							<input disabled={this.props.disabled} type="text" className={className} name="senderFirm" value={this.props.takedown.dmca.senderFirm || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.senderAddress" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label">Address</label>
							<input disabled={this.props.disabled} type="text" className={className} name="senderAddress[0]" value={this.props.takedown.dmca.senderAddress.get( 0, '' )} onChange={this.handleListChange.bind( this )} />
							<input disabled={this.props.disabled} type="text" className={className} name="senderAddress[1]" value={this.props.takedown.dmca.senderAddress.get( 1, '' )} onChange={this.handleListChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.senderCity" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label">City</label>
							<input disabled={this.props.disabled} type="text" className={className} name="senderCity" value={this.props.takedown.dmca.senderCity || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.senderState" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label className="form-control-label">State / Province</label>
							<input disabled={this.props.disabled} type="text" className={className} name="senderState" value={this.props.takedown.dmca.senderState || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.senderZip" error={this.props.takedown.error} render={ ( hasError, className ) => (
						<div>
							<label>Zip / Postal Code</label>
							<input disabled={this.props.disabled} type="text" className={className} name="senderZip" value={this.props.takedown.dmca.senderZip || ''} onChange={this.handleChange.bind( this )} />
						</div>
					) } />
					<FormGroup path="dmca.senderCountryCode" error={this.props.takedown.error} render={ () => (
						<div>
							<label className="form-control-label">Country</label>
							<Select disabled={this.props.disabled} name="senderCountryCode" options={countries} value={country} onChange={( data ) => this.updateField( 'senderCountryCode', data ? data.value : undefined )} />
						</div>
					) } />
				</fieldset>
				<fieldset className="form-group">
					<legend>Lumen</legend>
					<FormGroup path="dmca.lumenSend" error={this.props.takedown.error} render={ ( hasError ) => (
						<div className={hasError ? 'form-check has-danger' : 'form-check'}>
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
					) } />
					{lumenTitleField}
				</fieldset>
				<fieldset className="form-gorup">
					<legend>Wikimedia Foundation</legend>
					<FormGroup path="dmca.lumenSend" error={this.props.takedown.error} render={ ( hasError ) => (
						<div className={hasError ? 'form-check has-danger' : 'form-check'}>
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
					) } />
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
	contentLink: PropTypes.string,
	involved: PropTypes.arrayOf( PropTypes.instanceOf( User ) ).isRequired,
	files: PropTypes.instanceOf( Set ).isRequired,
	disabled: PropTypes.bool
};
