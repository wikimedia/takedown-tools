import { connect } from 'react-redux';
import { TakedownShowDmca } from './dmca';
import * as TakedownSelectors from 'app/selectors/takedown';

export const TakedownShowDmcaContainer = connect(
	() => {
		const getFiles = TakedownSelectors.makeGetFiles(),
			getInvolved = TakedownSelectors.makeGetInvolved();
		return ( state, props ) => {
			return {
				files: getFiles( state, props ),
				involved: getInvolved( state, props ),
				token: state.token
			};
		};
	}
)( TakedownShowDmca );
