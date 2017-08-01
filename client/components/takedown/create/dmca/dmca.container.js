import { connect } from 'react-redux';
import { TakedownCreateDmca } from './dmca';
import * as FileActions from 'app/actions/file';
import * as TakedownActions from 'app/actions/takedown';
import * as TakedownSelectors from 'app/selectors/takedown';

export const TakedownCreateDmcaContainer = connect(
	() => {
		const getFiles = TakedownSelectors.makeGetFiles(),
			getInonvolved = TakedownSelectors.makeGetInvolved(),
			getContentLink = TakedownSelectors.makeGetContentLink();
		return ( state, props ) => {
			props = {
				...props,
				takedown: state.takedown.create
			};

			return {
				takedown: state.takedown.create,
				contentLink: getContentLink( state, props ),
				involved: getInonvolved( state, props ),
				files: getFiles( state, props )
			};
		};
	},
	( dispatch ) => {
		return {
			updateTakedown: ( takedown ) => {
				return dispatch( TakedownActions.updateCreate( takedown ) );
			},
			addFiles: ( files ) => {
				return dispatch( FileActions.addMultiple( files ) );
			},
			deleteFile: ( file ) => {
				return dispatch( FileActions.deleteFile( file ) );
			}
		};
	}
)( TakedownCreateDmca );
