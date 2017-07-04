import { connect } from 'react-redux';
import { TakedownCreateCp } from './cp';
import * as TakedownActions from '../../../actions/takedown';

export const TakedownCreateCpContainer = connect(
	( state ) => {
		return {
			takedown: state.takedown.create
		};
	},
	( dispatch ) => {
		return {
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.updateCreate( takedown ) );
			}
		};
	}
)( TakedownCreateCp );
