import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Set } from 'immutable';
import Select from 'react-select';
import { SelectUsers } from '../fields/select-users';
import { Takedown, User } from '../../entity';
import * as TakedownSelectors from '../../selectors/takedown';
import * as TakedownActions from '../../actions/takedown';
import * as UserSelectors from '../../selectors/user';
import * as UserActions from '../../actions/user';
import * as SiteActions from '../../actions/site';
import * as SiteSelectors from '../../selectors/site';

export class TakedownCreate extends React.Component {

	componentWillMount() {
		this.props.fetchSites();
		this.componentWillReceiveProps( this.props );
	}

	componentWillReceiveProps( nextProps ) {
		let takedown;

		if ( nextProps.reporter.id && !nextProps.takedown.reporterId ) {
			takedown = nextProps.takedown.set( 'reporterId', nextProps.reporter.id );
			nextProps.updateTakedown( takedown );
		}
	}

	updateField( fieldName, value ) {
		const takedown = this.props.takedown
			.set( fieldName, value )
			.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
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
								<label htmlFor="siteId">Site</label>
								<Select name="siteId" options={this.props.siteOptions} value={this.props.takedown.siteId} onChange={( data ) => this.updateField( 'siteId', data.value )} />
							</div>
						</div>
						<div className="form-group row">
							<div className="col">
								<label htmlFor="involvedIds">Involved Users</label>
								<SelectUsers disabled={disabled} name="involvedIds" value={this.props.involved} users={ this.props.users.toArray() } onChange={this.updateInvolved.bind( this )} />
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
	users: PropTypes.instanceOf( Set ).isRequired,
	involved: PropTypes.arrayOf( PropTypes.instanceOf( User ) ).isRequired,
	siteOptions: PropTypes.arrayOf( PropTypes.shape( {
		label: PropTypes.string,
		value: PropTypes.string
	} ) ),
	reporter: PropTypes.instanceOf( User ).isRequired,
	updateTakedown: PropTypes.func.isRequired,
	saveTakedown: PropTypes.func.isRequired,
	addUsers: PropTypes.func.isRequired
};

export const TakedownCreateContainer = connect(
	() => {
		const getInvovled = TakedownSelectors.makeGetInvolved();
		return ( state, props ) => {
			props = {
				...props,
				takedown: state.takedown.create
			};

			return {
				reporter: UserSelectors.getAuthUser( state ),
				involved: getInvovled( state, props ),
				takedown: state.takedown.create,
				users: state.user.list,
				siteOptions: SiteSelectors.getSiteOptions( state )
			};
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
