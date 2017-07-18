import { connect } from 'react-redux';
import * as TakedownActions from 'app/actions/takedown';
import { TakedownShowDmcaUserNotice } from './user-notice';

export const TakedownShowDmcaUserNoticeContainer = connect(
	undefined,
	( dispatch ) => {
		return {
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.update( takedown ) );
			},
			saveUserNotice: ( takedown, user ) => {
				return dispatch( TakedownActions.saveDmcaUserNotice( takedown, user ) );
			}
		};
	}
)( TakedownShowDmcaUserNotice );
