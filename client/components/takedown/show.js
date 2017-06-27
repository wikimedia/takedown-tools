import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import * as TakedownSelectors from '../../selectors/takedown';
import * as TakedownActions from '../../actions/takedown';
import { Takedown, User, Site } from '../../entity';
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

		let site;

		if ( this.props.site.id ) {
			site = (
				<span>
					{this.props.site.name} ({this.props.site.domain})
				</span>
			);
		}

		return (
			<div className="row">
				<div className="col">
					<div className="row">
						<div className="col">
							<h2>Takedown #{this.props.takedown.id}</h2>
						</div>
					</div>
					<div className="row pb-2">
						<div className="col-2">
							<strong>Reporter</strong>
						</div>
						<div className="col-10">
							{this.props.reporter.username}
						</div>
					</div>
					<div className="row pb-2">
						<div className="col-2">
							<strong>Site</strong>
						</div>
						<div className="col-10">
							{site}
						</div>
					</div>
					<div className="row pb-2">
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
	reporter: PropTypes.instanceOf( User ),
	site: PropTypes.instanceOf( Site )
};

export const TakedownShowContainer = connect(
	() => {
		const getTakedown = TakedownSelectors.makeGetTakedown(),
			getInvolved = TakedownSelectors.makeGetInvolved(),
			getReporter = TakedownSelectors.makeGetReporter(),
			getSite = TakedownSelectors.makeGetSite();
		return ( state, props ) => {
			const takedown = getTakedown( state, props );

			props = {
				...props,
				takedown: takedown
			};

			return {
				status: state.takedown.status,
				takedown: takedown,
				involved: getInvolved( state, props ),
				reporter: getReporter( state, props ),
				site: getSite( state, props )
			};
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
