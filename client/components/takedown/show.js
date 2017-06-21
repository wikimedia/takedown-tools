import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { fetchTakedown } from '../../actions/takedown';
import { Takedown } from '../../entity';
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

		return (
			<div className="row">
				<div className="col">
					<h2>{this.props.takedown.id}</h2>
				</div>
			</div>
		);
	}
}

TakedownShow.propTypes = {
	status: PropTypes.string.isRequired,
	onComponentWillMount: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown )
};

export const TakedownShowContainer = connect(
	( state, ownProps ) => {
		const id = parseInt( ownProps.match.params.id );
		return {
			status: state.takedown.status,
			takedown: state.takedown.list.find( ( takedown ) => {
				return id === takedown.id;
			} )
		};
	},
	( dispatch, ownProps ) => {
		const id = parseInt( ownProps.match.params.id );
		return {
			onComponentWillMount: () => {
				dispatch( fetchTakedown( id ) );
			}
		};
	}
)( TakedownShow );
