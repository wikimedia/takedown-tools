import { connect } from 'react-redux';
import { TakedownShowDmca } from './dmca';
import * as TakedownSelectors from '../../../selectors/takedown';

export const TakedownShowDmcaContainer = connect(
	() => {
		const getFiles = TakedownSelectors.makeGetFiles();
		return ( state, props ) => {
			return {
				files: getFiles( state, props ),
				token: state.token
			};
		};
	}
)( TakedownShowDmca );
