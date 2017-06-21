import React from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import * as TakedownActions from '../../actions/takedown';
import { Takedown, User } from '../../entity';
import { Loading } from '../loading';

export class TakedownIndex extends React.Component {

	componentWillMount() {
		if ( this.props.status !== 'done' ) {
			this.props.onComponentWillMount();
		}
	}

	render() {
		const takedowns = this.props.takedowns.map( ( takedown ) => {
			const reporter = this.props.users.find( ( user ) => {
				return takedown.reporterId === user.id;
			} );
			let reporterName,
				created;

			if ( reporter ) {
				reporterName = reporter.username;
			}

			if ( takedown.created ) {
				created = takedown.created.local().format('l LT');
			}

			return (
				<tr key={takedown.id}>
					<td><Link to={'/takedown/' + takedown.id}>{takedown.id}</Link></td>
					<td>{reporterName}</td>
					<td>{created}</td>
				</tr>
			);
		} );

		let loading;

		if ( this.props.status === 'fetching' ) {
			loading = (
				<Loading />
			);
		}

		return (
			<div className="row">
				<div className="col">
					<table className="table table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>Reporter</th>
								<th>Created</th>
							</tr>
						</thead>
						<tbody>
							{takedowns}
						</tbody>
					</table>
					{loading}
				</div>
			</div>
		);
	}
}

TakedownIndex.propTypes = {
	onComponentWillMount: PropTypes.func.isRequired,
	status: PropTypes.string.isRequired,
	takedowns: PropTypes.arrayOf( PropTypes.instanceOf( Takedown ) ),
	users: PropTypes.arrayOf( PropTypes.instanceOf( User ) )
};

export const TakedownIndexContainer = connect(
	( state ) => {
		return {
			status: state.takedown.status,
			takedowns: state.takedown.list.filter( ( takedown ) => {
				return !takedown.error;
			} ),
			users: state.user.list
		};
	},
	( dispatch ) => {
		return {
			onComponentWillMount: () => {
				dispatch( TakedownActions.fetchList() );
			}
		};
	}
)( TakedownIndex );
