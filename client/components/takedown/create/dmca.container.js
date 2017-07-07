import { connect } from 'react-redux';
import { TakedownCreateDmca } from './dmca';
import * as FileActions from '../../../actions/file';
import * as TakedownActions from '../../../actions/takedown';
import * as TakedownSelectors from '../../../selectors/takedown';

export const TakedownCreateDmcaContainer = connect(
	() => {
		const getSite = TakedownSelectors.makeGetSite(),
			getFiles = TakedownSelectors.makeGetFiles();
		return ( state, props ) => {
			props = {
				...props,
				takedown: state.takedown.create
			};

			return {
				takedown: state.takedown.create,
				site: getSite( state, props ),
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
