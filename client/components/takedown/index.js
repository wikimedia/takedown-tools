import React from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom'

export default class Takedown extends React.Component {

	componentWillMount() {
		this.props.onComponentWillMount();
	}

	render() {
		const takedowns = this.props.takedowns.map( ( takedown ) => {
			return (
				<tr key={takedown.id}>
					<td><Link to={'/takedown/' + takedown.id}>{takedown.id}</Link></td>
				</tr>
			);
		} );

		return (
			<div className="row">
				<div className="col">
					<table className="table table-responsive">
						<thead>
							<tr>
								<th>#</th>
							</tr>
						</thead>
						<tbody>
							{takedowns}
						</tbody>
					</table>
				</div>
			</div>
		);
	}
}

Takedown.propTypes = {
	onComponentWillMount: PropTypes.func.isRequired,
	takedowns: PropTypes.array
};
