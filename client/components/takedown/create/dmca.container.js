import { connect } from 'react-redux';
import { TakedownCreateDmca } from './dmca';
import * as TakedownActions from '../../../actions/takedown';

export const TakedownCreateDmcaContainer = connect(
	undefined,
	( dispatch ) => {
		return {
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.updateCreate( takedown ) );
			}
		};
	}
)( TakedownCreateDmca );
