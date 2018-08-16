import React from 'react';
import PropTypes from 'prop-types';
import { File } from 'app/entities/file';
import { Takedown } from 'app/entities/takedown/takedown';
import { DatePicker } from 'app/components/fields/date-picker';
import { FormGroup } from 'app/components/fields/form-group';
import { FileProgress } from 'app/components/file-progress';
import { removeErrors } from 'app/utils';
import 'fileicon.css/fileicon.css';

export class TakedownCreateCpFile extends React.Component {
	updateFile( file, fieldName, value ) {
		file = file.set( fieldName, value );

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

		takedown = removeErrors( takedown, `cp.files[${key}].${fieldName}` );

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
		const ext = this.props.file.name.split( '.' ).pop(),
			index = this.props.index;

		let exif,
			removeClasses = [ 'btn', 'btn-outline-danger', 'btn-sm' ];

		if ( this.props.file.exif ) {
			exif = (
				<FormGroup path={`cp.files[${index}].exif`} error={this.props.takedown.error} render={() => (
					<div>
						<label className="form-control-label">Exif Data</label>
						<div>
							<pre className="small bg-faded pt-2 pb-2 pl-2 pr-2">
								<code>
									{JSON.stringify( this.props.file.exif, undefined, 2 )}
								</code>
							</pre>
						</div>
					</div>
				)} />
			);
		}

		if ( this.props.disabled ) {
			removeClasses = [
				...removeClasses,
				'disabled'
			];
		}

		return (
			<FileProgress className="form-control p-0 mb-2" file={this.props.file}>
				<div className="row pb-4 pt-2">
					<div className="col-1">
						<div className="file-icon file-icon-lg ml-3 mt-2" data-type={ext}></div>
					</div>
					<div className="col-10">
						<div>
							<div className="form-control-static">{this.props.file.name}</div>
						</div>
						<FormGroup path={`cp.files[${index}].uploaded`} error={this.props.takedown.error} render={() => (
							<div>
								<label className="form-control-label">Uploaded</label>
								<DatePicker time={true} disabled={this.props.disabled} value={this.props.file.uploaded} onChange={( value ) => this.updateFile( this.props.file, 'uploaded', value )} />
							</div>
						)} />
						<FormGroup path={`cp.files[${index}].ip`} error={this.props.takedown.error} render={() => (
							<div>
								<label className="form-control-label" htmlFor={`files[${index}][ip]`}>IP Address</label>
								<input type="text" disabled={this.props.disabled} className="form-control" name={`files[${index}][ip]`} value={this.props.file.ip || ''} onChange={( event ) => this.updateFile( this.props.file, 'ip', event.target.value )} />
							</div>
						)} />
						{exif}
						<button type="button" className={removeClasses.join( ' ' )} onClick={() => this.removeFile( this.props.file )}>Remove</button>
					</div>
				</div>
			</FileProgress>
		);
	}
}

TakedownCreateCpFile.propTypes = {
	file: PropTypes.instanceOf( File ).isRequired,
	index: PropTypes.number.isRequired,
	updateTakedown: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired,
	disabled: PropTypes.bool
};
