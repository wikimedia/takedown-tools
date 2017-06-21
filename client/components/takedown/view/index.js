import React from 'react';
import PropTypes from 'prop-types';

export default class TakedownView extends React.Component {
	render() {
		// @TODO Ensure that takedown has all it's properties.
		return (
			<div className="row">
				<div className="col">
					<h2>{this.props.takedown.id}</h2>
				</div>
			</div>
		);
	}
}

TakedownView.propTypes = {
	takedown: PropTypes.record
};
