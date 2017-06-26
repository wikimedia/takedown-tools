import React from 'react';
import { Observable } from 'rxjs';
import 'rxjs/add/observable/fromEvent';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import * as moment from 'moment';
import * as TakedownActions from '../../actions/takedown';
import { Set } from 'immutable';
import { Loading } from '../loading';

export class TakedownIndex extends React.Component {
	componentDidMount() {
		if ( this.props.status !== 'done' && this.isBottomVisable( this.table ) ) {
			this.props.fetchList();
		}

		this.scrollSubscription = Observable.fromEvent( window, 'scroll' )
			.takeWhile( () => {
				return this.props.status !== 'done';
			} )
			.debounceTime( 250 )
			.filter( () => {
				return this.isBottomVisable( this.table );
			} )
			.subscribe( () => {
				this.props.fetchList();
			} );
	}

	componentWillUnmount() {
		this.scrollSubscription.unsubscribe();
	}

	componentDidUpdate( prevProps ) {
		if ( this.props.status !== 'done' && prevProps.takedowns.size !== this.props.takedowns.size && this.isBottomVisable( this.table ) ) {
			this.props.fetchList();
		}
	}

	isBottomVisable( element ) {
		const rect = element.getBoundingClientRect(),
			offset = 100;
		return ( rect.bottom - offset ) <= ( window.innerHeight || document.documentElement.clientHeight );
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
				created = moment.utc( takedown.created ).local().format( 'l LT' );
			}

			return (
				<tr key={takedown.id}>
					<th scope="row"><Link to={'/takedown/' + takedown.id}>{takedown.id}</Link></th>
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
			<div>
				<div className="row mb-2">
					<div className="col-11">
						<h2>Takedowns</h2>
					</div>
					<div className="col-1 text-right">
						<Link to="/takedown/create" className="btn btn-primary btn-sm">+ New</Link>
					</div>
				</div>
				<div className="row">
					<div className="col">
						<table ref={( table ) => { this.table = table; }} className="table table-bordered">
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
			</div>
		);
	}
}

TakedownIndex.propTypes = {
	fetchList: PropTypes.func.isRequired,
	status: PropTypes.string.isRequired,
	takedowns: PropTypes.instanceOf( Set ),
	users: PropTypes.instanceOf( Set )
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
			fetchList: () => {
				dispatch( TakedownActions.fetchList() );
			}
		};
	}
)( TakedownIndex );
