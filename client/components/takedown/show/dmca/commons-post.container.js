import { connect } from 'react-redux';
import * as TakedownActions from 'app/actions/takedown';
import { TakedownShowDmcaCommonsPost } from './commons-post';
import * as TakedownSelectors from 'app/selectors/takedown';

export const TakedownShowDmcaCommonsPostContainer = connect(
	() => {
		const getSite = TakedownSelectors.makeGetSite();
		return ( state, props ) => {
			return {
				site: getSite( state, props )
			};
		};
	},
	( dispatch ) => {
		return {
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.update( takedown ) );
			},
			savePost: ( takedown, post ) => {
				return dispatch( TakedownActions.savePost( takedown, post ) );
			}
		};
	}
)( TakedownShowDmcaCommonsPost );
