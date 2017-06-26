import React from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import * as moment from 'moment';
import * as TakedownSelectors from '../../../selectors/takedown';
import { Takedown, User } from '../../../entity';

export class TakedownIndexRow extends React.Component {
	render() {
		let created;

		if ( this.props.takedown.created ) {
			created = moment.utc( this.props.takedown.created ).local().format( 'l LT' );
		}

		return (
			<tr>
				<th scope="row"><Link to={'/takedown/' + this.props.takedown.id}>{this.props.takedown.id}</Link></th>
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

export const TakedownIndexRowContainer = connect(
	() => {
		const getReporter = TakedownSelectors.makeGetReporter();
		return ( state, props ) => {
			return {
				reporter: getReporter( state, props )
			};
		};
	}
)( TakedownIndexRow );
