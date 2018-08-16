import React from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import * as moment from 'moment';
import { User } from '../../../entities/user';
import { Takedown } from '../../../entities/takedown/takedown';

export class TakedownIndexRow extends React.Component {
	render() {
		let created,
			type;

		if ( this.props.takedown.created ) {
			created = moment.utc( this.props.takedown.created ).format( 'l LT' );
		}

		if ( this.props.takedown.type ) {
			switch ( this.props.takedown.type ) {
				case 'dmca':
					type = 'DMCA';
					break;
				case 'cp':
					type = 'Child Protection';
					break;
			}
		}

		return (
			<tr>
				<th scope="row"><Link to={'/takedown/' + this.props.takedown.id}>{this.props.takedown.id}</Link></th>
				<td>{type}</td>
				<td>{this.props.reporter.username}</td>
				<td>{created}</td>
			</tr>
		);
	}
}

TakedownIndexRow.propTypes = {
	takedown: PropTypes.instanceOf( Takedown ).isRequired,
	reporter: PropTypes.instanceOf( User ).isRequired
};
