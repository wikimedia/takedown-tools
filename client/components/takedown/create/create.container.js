import { connect } from 'react-redux';
import { TakedownCreate } from './create';
import * as TakedownSelectors from '../../../selectors/takedown';
import * as TakedownActions from '../../../actions/takedown';
import * as UserSelectors from '../../../selectors/user';
import * as UserActions from '../../../actions/user';
import * as SiteActions from '../../../actions/site';
import * as SiteSelectors from '../../../selectors/site';

export const TakedownCreateContainer = connect(
	() => {
		const getInvovled = TakedownSelectors.makeGetInvolved();
		return ( state, props ) => {
			props = {
				...props,
				takedown: state.takedown.create
			};

			return {
				reporter: UserSelectors.getAuthUser( state ),
				involved: getInvovled( state, props ),
				takedown: state.takedown.create,
				users: state.user.list,
				siteOptions: SiteSelectors.getSiteOptions( state )
			};
		};
	},
	( dispatch ) => {
		return {
			fetchSites: () => {
				return dispatch( SiteActions.fetchAll() );
			},
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.updateCreate( takedown ) );
			},
			saveTakedown: () => {
				return dispatch( TakedownActions.saveCreate() );
			},
			addUsers: ( users ) => {
				return dispatch( UserActions.addMultiple( users ) );
			}
		};
	}
)( TakedownCreate );
