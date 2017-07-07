import React from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import { Set } from 'immutable';
import { SelectPages } from '../../fields/select-pages';
import { ListField } from '../../fields/list';
import { FileField } from '../../fields/file';
import { Takedown } from '../../../entities/takedown/takedown';
import { Site } from '../../../entities/site';
import { CountrySet } from '../../../entities/country.set';
import { DatePicker } from '../../fields/date-picker';

export class TakedownCreateDmca extends React.Component {

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

	render() {
		const countries = CountrySet.map( ( country ) => {
			return {
				value: country.id,
				label: country.name
			};
		} ).toArray();

		let country,
			lumenTitleField;

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
					<FileField value={this.props.files} onAddFiles={this.addFiles.bind( this )} onRemoveFile={this.removeFile.bind( this )} />
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
	files: PropTypes.instanceOf( Set ).isRequired,
	disabled: PropTypes.bool
};
