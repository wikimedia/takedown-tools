import React from 'react';
import PropTypes from 'prop-types';
import { Observable } from 'rxjs';
import 'rxjs/add/observable/fromEvent';
import { Link } from 'react-router-dom';
import { Set } from 'immutable';
import { TakedownIndexRowContainer } from './row.container';
import { Loading } from '../../loading';

export class TakedownIndex extends React.Component {
	componentDidMount() {
		if ( this.props.status !== 'done' && this.isBottomVisable( this.table ) ) {
			this.props.fetchList();
		}

		// Create the infinite scroll.
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

		this.resizeSubscription = Observable.fromEvent( window, 'resize' )
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
		this.resizeSubscription.unsubscribe();
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
			return (
				<TakedownIndexRowContainer key={takedown.id} takedown={takedown} />
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
									<th>Type</th>
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
	takedowns: PropTypes.instanceOf( Set )
};
