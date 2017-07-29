import React from 'react';
import PropTypes from 'prop-types';

export class FileProgress extends React.Component {
	render() {
		let progressStyle,
			progressClasses = [ 'progress' ];

		switch ( this.props.file.status ) {
			case 'uploading':
				progressStyle = {
					width: this.props.file.progress + '%'
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
			<div className={this.props.className}>
				<div className={progressClasses.join( ' ' )} style={progressStyle}></div>
				{this.props.children}
			</div>
		);
	}
}

FileProgress.propTypes = {
	file: PropTypes.instanceOf( File ).isRequired,
	children: PropTypes.node.isRequired,
	className: PropTypes.string
};
