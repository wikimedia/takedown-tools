import { connect } from 'react-redux';
import { TakedownIndexRow } from './row';
import * as TakedownSelectors from '../../../selectors/takedown';

export const TakedownIndexRowContainer = connect(
	() => {
		const getReporter = TakedownSelectors.makeGetReporter();
		return ( state, props ) => {
			return {
				reporter: getReporter( state, props )
			};
		};
	}
)( TakedownIndexRow );
