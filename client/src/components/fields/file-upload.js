import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import { FileField } from './file';
import { FileProgress } from 'app/components/file-progress';
import 'fileicon.css/fileicon.css';

export class FileUploadField extends React.Component {
	render() {
		let files;

		if ( this.props.value.size ) {
			files = this.props.value.map( ( file ) => {
				const ext = file.name.split( '.' ).pop();

				return (
					<FileProgress className="input-group mb-2" file={file} key={file.id}>
						<span className="form-control flex-row justify-content-start align-items-center">
							<span className="file-icon mr-2" data-type={ext}></span>
							<span>{file.name}</span>
						</span>
						<span className="input-group-btn">
							<button className="btn btn-secondary" type="button" onClick={() => this.props.onRemoveFile( file )}>×</button>
						</span>
					</FileProgress>
				);
			} );
		}

		return (
			<div>
				<FileField onAddFiles={this.props.onAddFiles} />
				{files}
			</div>
		);
	}
}

FileUploadField.propTypes = {
	value: PropTypes.instanceOf( Set ).isRequired,
	onAddFiles: PropTypes.func.isRequired,
	onRemoveFile: PropTypes.func.isRequired
};
