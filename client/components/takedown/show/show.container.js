import { connect } from 'react-redux';
import { TakedownShow } from './show';
import * as TakedownSelectors from '../../../selectors/takedown';
import * as TakedownActions from '../../../actions/takedown';

export const TakedownShowContainer = connect(
	() => {
		const getTakedown = TakedownSelectors.makeGetTakedown(),
			getInvolved = TakedownSelectors.makeGetInvolved(),
			getReporter = TakedownSelectors.makeGetReporter(),
			getSite = TakedownSelectors.makeGetSite(),
			getMetadata = TakedownSelectors.makeGetMetadata();
		return ( state, props ) => {
			const takedown = getTakedown( state, props );

			props = {
				...props,
				takedown: takedown
			};

			return {
				status: state.takedown.status,
				takedown: takedown,
				involved: getInvolved( state, props ),
				reporter: getReporter( state, props ),
				metadata: getMetadata( state, props ),
				site: getSite( state, props )
			};
		};
	},
	( dispatch, ownProps ) => {
		const id = parseInt( ownProps.match.params.id );
		return {
			onComponentWillMount: () => {
				dispatch( TakedownActions.fetch( id ) );
			}
		};
	}
)( TakedownShow );
