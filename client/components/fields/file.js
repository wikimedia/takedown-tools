import React from 'react';
import PropTypes from 'prop-types';
import Dropzone from 'react-dropzone';
import { Set } from 'immutable';
import * as shortid from 'shortid';
import { File } from '../../entities/file';
import 'fileicon.css/fileicon.css';

export class FileField extends React.Component {

	constructor( props ) {
		super( props );

		this.state = {
			active: false
		};
	}

	onDrop( acceptedFiles ) {
		const files = acceptedFiles.map( ( file ) => {
			return new File( {
				id: shortid.generate(),
				status: 'local',
				name: file.name,
				file: file
			} );
		} );

		this.props.onAddFiles( new Set( files ) );

		this.setState( {
			...this.state,
			active: false
		} );
	}

	onDragOver() {
		if ( this.state.active ) {
			return;
		}

		this.setState( {
			...this.state,
			active: true
		} );
	}

	onDragLeave() {
		if ( !this.state.active ) {
			return;
		}

		this.setState( {
			...this.state,
			active: false
		} );
	}

	render() {
		let classes = [ 'dropzone' ],
			files;

		if ( this.props.value.size ) {
			files = this.props.value.map( ( file ) => {
				const ext = file.name.split( '.' ).pop();

				let progressStyle,
					progressClasses = [ 'progress' ];

				switch ( file.status ) {
					case 'uploading':
						progressStyle = {
							width: file.progress + '%'
						};
						break;

					case 'ready':
						progressClasses = [
							...progressClasses,
							'done'
						];
						break;
					case 'error':
						progressClasses = [
							...progressClasses,
							'error'
						];
						break;
				}

				return (
					<div className="input-group mb-2" key={file.id}>
						<span className={progressClasses.join( ' ' )} style={progressStyle}></span>
						<span className="form-control flex-row justify-content-start align-items-center">
							<span className="file-icon mr-2" data-type={ext}></span>
							<span>{file.name}</span>
						</span>
						<span className="input-group-btn">
							<button className="btn btn-secondary" type="button" onClick={() => this.props.onRemoveFile( file )}>×</button>
						</span>
					</div>
				);
			} );
		}

		if ( this.state.active ) {
			classes = [
				...classes,
				'active'
			];
		}
		return (
			<div>
				<Dropzone
					className={classes.join( ' ' )}
					onDrop={this.onDrop.bind( this )}
					onDragOver={this.onDragOver.bind( this )}
					onDragLeave={this.onDragLeave.bind( this )}
					disablePreview={true}
					style={{}}
				>
					<i className="material-icons">file_upload</i>
				</Dropzone>
				{files}
			</div>
		);
	}
}

FileField.propTypes = {
	value: PropTypes.instanceOf( Set ).isRequired,
	onAddFiles: PropTypes.func.isRequired,
	onRemoveFile: PropTypes.func.isRequired
};
