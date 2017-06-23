import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { SelectUsers } from '../fields/select-users';
import { Takedown, User } from '../../entity';
import { parseJwt } from '../../utils';
import * as TakedownActions from '../../actions/takedown';
import * as UserActions from '../../actions/user';
import * as SiteActions from '../../actions/site';

export class TakedownCreate extends React.Component {

	componentWillMount() {
		this.props.fetchSites();
		this.componentWillReceiveProps( this.props );
	}

	componentWillReceiveProps( nextProps ) {
		let takedown;

		if ( nextProps.reporterId && !nextProps.takedown.reporterId ) {
			takedown = nextProps.takedown.set( 'reporterId', nextProps.reporterId );
			nextProps.updateTakedown( takedown );
		}
	}

	updateInvolved( involved ) {
		const takedown = this.props.takedown
			.set( 'involvedIds', involved.map( ( user ) => {
				return user.id;
			} ) )
			.set( 'status', 'dirty' );

		this.props.addUsers( involved );
		this.props.updateTakedown( takedown );
	}

	onSubmit( e ) {
		e.preventDefault();
		this.props.saveTakedown();
	}

	render() {
		const involved = this.props.takedown.involvedIds.map( ( id ) => {
			return this.props.users.find( ( user ) => {
				return user.id === id;
			} );
		} ).filter( ( user ) => {
			return typeof user !== 'undefined';
		} );
		let submitClass = 'btn btn-primary',
			submitDisabled = false,
			disabled = false;

		switch ( this.props.takedown.status ) {
			case 'error':
				submitClass = 'btn btn-danger';
				break;
			case 'saving':
				disabled = true;
				submitDisabled = true;
				break;
			case 'clean':
				submitDisabled = true;
				break;
		}

		return (
			<div className="row">
				<div className="col">
					<h2>Create Takedown</h2>
					<form onSubmit={this.onSubmit.bind( this )}>
						<div className="form-group row">
							<div className="col">
								<label htmlFor="involvedIds">Involved Users</label>
								<SelectUsers disabled={disabled} name="involvedIds" value={involved} users={this.props.users} onChange={this.updateInvolved.bind( this )} />
							</div>
						</div>
						<div className="form-group row">
							<div className="col text-right">
								<input type="submit" className={submitClass} disabled={submitDisabled} value="Save" />
							</div>
						</div>
					</form>
				</div>
			</div>
		);
	}
}

TakedownCreate.propTypes = {
	fetchSites: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired,
	users: PropTypes.arrayOf( PropTypes.instanceOf( User ) ).isRequired,
	reporterId: PropTypes.number,
	updateTakedown: PropTypes.func.isRequired,
	saveTakedown: PropTypes.func.isRequired,
	addUsers: PropTypes.func.isRequired
};

export const TakedownCreateContainer = connect(
	( state ) => {
		let reporterId;

		if ( state.token ) {
			reporterId = parseJwt( state.token ).id;
		}

		return {
			reporterId: reporterId,
			takedown: state.takedown.create,
			users: state.user.list
		};
	},
	( dispatch ) => {
		return {
			fetchSites: () => {
				return dispatch( SiteActions.fetchAll() );
			},
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.updateCreate( takedown ) );
			},
			saveTakedown: () => {
				return dispatch( TakedownActions.saveCreate() );
			},
			addUsers: ( users ) => {
				return dispatch( UserActions.addMultiple( users ) );
			}
		};
	}
)( TakedownCreate );
