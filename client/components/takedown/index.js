import React from 'react';
import PropTypes from 'prop-types';

export default class Takedown extends React.Component {

	componentWillMount() {
		this.props.onComponentWillMount();
	}

	render() {
		return (
			<div className="row">
				<div className="col">
					<table className="table table-responsive">
						<thead>
							<tr>
								<th>#</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		);
	}
}

Takedown.propTypes = {
	onComponentWillMount: PropTypes.func.isRequired
};
