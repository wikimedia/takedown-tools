import { connect } from 'react-redux';
import TakedownView from './index';

export default connect(
	( state, ownProps ) => {
		const id = parseInt( ownProps.match.params.id );
		return {
			takedown: state.takedown.list.find( ( takedown ) => {
				return id === takedown.id;
			} )
		};
	}
)( TakedownView );
