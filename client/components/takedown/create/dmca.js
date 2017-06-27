import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Takedown } from '../../../entity';
import * as TakedownActions from '../../../actions/takedown';

export class TakedownCreateDmca extends React.Component {

	updateField( fieldName, value ) {
		const dmca = this.props.takedown.dmca
				.set( fieldName, value ),
			takedown = this.props.takedown
				.set( 'dmca', dmca )
				.set( 'status', 'dirty' );

		this.props.updateTakedown( takedown );
	}

	render() {
		return (
			<h1>DMCA</h1>
		);
	}
}

TakedownCreateDmca.propTypes = {
	updateTakedown: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired
};

export const TakedownCreateDmcaContainer = connect(
	( dispatch ) => {
		return {
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.updateCreate( takedown ) );
			}
		};
	}
)( TakedownCreateDmca );
