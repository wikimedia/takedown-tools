import { connect } from 'react-redux';
import { TakedownIndex } from './index';
import * as TakedownSelectors from '../../../selectors/takedown';
import * as TakedownActions from '../../../actions/takedown';

export const TakedownIndexContainer = connect(
	() => {
		const getTakedownList = TakedownSelectors.makeGetTakedownList();
		return ( state, props ) => {
			return {
				status: state.takedown.status,
				takedowns: getTakedownList( state, props )
			};
		};
	},
	( dispatch ) => {
		return {
			fetchList: () => {
				dispatch( TakedownActions.fetchList() );
			}
		};
	}
)( TakedownIndex );
