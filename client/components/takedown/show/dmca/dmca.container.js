import { connect } from 'react-redux';
import { TakedownShowDmca } from './dmca';
import * as TakedownSelectors from 'app/selectors/takedown';

export const TakedownShowDmcaContainer = connect(
	() => {
		const getFiles = TakedownSelectors.makeGetFiles(),
			getNotices = TakedownSelectors.makeGetNotices();
		return ( state, props ) => {
			return {
				files: getFiles( state, props ),
				notices: getNotices( state, props ),
				token: state.token
			};
		};
	}
)( TakedownShowDmca );
