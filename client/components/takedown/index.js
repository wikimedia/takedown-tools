import React from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import { fetchTakedownList } from '../../actions/takedown';
import { Takedown } from '../../entity';
import { Loading } from '../loading';

export class TakedownIndex extends React.Component {

	componentWillMount() {
		if ( this.props.status !== 'done' ) {
			this.props.onComponentWillMount();
		}
	}

	render() {
		const takedowns = this.props.takedowns.map( ( takedown ) => {
			return (
				<tr key={takedown.id}>
					<td><Link to={'/takedown/' + takedown.id}>{takedown.id}</Link></td>
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
					<table className="table table-responsive">
						<thead>
							<tr>
								<th>#</th>
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
	takedowns: PropTypes.arrayOf( PropTypes.instanceOf( Takedown ) )
};

export const TakedownIndexContainer = connect(
	( state ) => {
		return {
			status: state.takedown.status,
			takedowns: state.takedown.list.filter( ( takedown ) => {
				return !takedown.error;
			} )
		};
	},
	( dispatch ) => {
		return {
			onComponentWillMount: () => {
				dispatch( fetchTakedownList() );
			}
		};
	}
)( TakedownIndex );
