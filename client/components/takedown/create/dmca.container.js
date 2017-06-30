import { connect } from 'react-redux';
import { TakedownCreateDmca } from './dmca';
import * as TakedownActions from '../../../actions/takedown';
import * as TakedownSelectors from '../../../selectors/takedown';

export const TakedownCreateDmcaContainer = connect(
	() => {
		const getSite = TakedownSelectors.makeGetSite();
		return ( state, props ) => {
			props = {
				...props,
				takedown: state.takedown.create
			};

			return {
				takedown: state.takedown.create,
				site: getSite( state, props )
			};
		};
	},
	( dispatch ) => {
		return {
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.updateCreate( takedown ) );
			}
		};
	}
)( TakedownCreateDmca );
