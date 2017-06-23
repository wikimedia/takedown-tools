import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import * as TakedownActions from '../../actions/takedown';
import { Takedown, User } from '../../entity';
import { Loading } from '../loading';
import { Error } from '../error';

export class TakedownShow extends React.Component {
	componentWillMount() {
		if ( this.props.status !== 'done' ) {
			this.props.onComponentWillMount();
		}
	}
	render() {
		if ( !this.props.takedown ) {
			if ( this.props.status === 'fetching' ) {
				return (
					<Loading />
				);
			} else if ( this.props.status === 'done' ) {
				return (
					<Error code={404} />
				);
			} else {
				return null;
			}
		}

		if ( this.props.takedown.error ) {
			return (
				<Error code={this.props.takedown.error} />
			);
		}

		const involved = this.props.involved.map( ( user ) => {
			return (
				<div key={user.id}>{user.username}</div>
			);
		} );

		return (
			<div className="row">
				<div className="col">
					<div className="row">
						<div className="col">
							<h2>Takedown #{this.props.takedown.id}</h2>
						</div>
					</div>
					<div className="row">
						<div className="col-2">
							<strong>Reporter</strong>
						</div>
						<div className="col-10">
							{this.props.reporterName}
						</div>
					</div>
					<div className="row">
						<div className="col-2">
							<strong>Involved Users</strong>
						</div>
						<div className="col-10">
							{involved}
						</div>
					</div>
				</div>
			</div>
		);
	}
}

TakedownShow.propTypes = {
	status: PropTypes.string.isRequired,
	onComponentWillMount: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ),
	involved: PropTypes.arrayOf( PropTypes.instanceOf( User ) ),
	reporterName: PropTypes.string
};

export const TakedownShowContainer = connect(
	( state, ownProps ) => {
		const id = parseInt( ownProps.match.params.id ),
			takedown = state.takedown.list.find( ( takedown ) => {
				return id === takedown.id;
			} );
		let involved = [],
			reporter,
			reporterName;

		if ( takedown ) {
			// Get invovled users.
			involved = takedown.involvedIds.map( ( id ) => {
				return state.user.list.find( ( user ) => {
					return user.id === id;
				} );
			} ).filter( ( user ) => {
				return typeof user !== 'undefined';
			} );

			// Get Reporter.
			if ( takedown.reporterId ) {
				reporter = state.user.list.find( ( user ) => {
					return user.id === takedown.reporterId;
				} );
				if ( reporter ) {
					reporterName = reporter.username;
				}
			}
		}
		return {
			status: state.takedown.status,
			takedown: takedown,
			involved: involved,
			reporterName: reporterName
		};
	},
	( dispatch, ownProps ) => {
		const id = parseInt( ownProps.match.params.id );
		return {
			onComponentWillMount: () => {
				dispatch( TakedownActions.fetch( id ) );
			}
		};
	}
)( TakedownShow );
