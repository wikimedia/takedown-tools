import React from 'react';
import PropTypes from 'prop-types';
import { Takedown } from '../../../entities/takedown/takedown';

export class TakedownShowCp extends React.Component {
	render() {
		return (
			<tbody className="border-top-0">
				<tr>
					<td>Approved</td>
					<td>{this.props.takedown.cp.approved ? 'Yes' : 'No'}</td>
				</tr>
				<tr>
					<td>Approver</td>
					<td>{this.props.takedown.cp.approverName}</td>
				</tr>
			</tbody>
		);
	}
}

TakedownShowCp.propTypes = {
	takedown: PropTypes.instanceOf( Takedown )
};
