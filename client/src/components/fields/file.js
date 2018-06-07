import React from 'react';
import PropTypes from 'prop-types';
import Dropzone from 'react-dropzone';
import { Set } from 'immutable';
import * as shortid from 'shortid';
import { File } from 'app/entities/file';

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
		let classes = [ 'dropzone' ];

		if ( this.state.active ) {
			classes = [
				...classes,
				'active'
			];
		}
		return (
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
		);
	}
}

FileField.propTypes = {
	onAddFiles: PropTypes.func.isRequired
};
