import { connect } from 'react-redux';
import { fetchTakedownList } from '../../actions/takedown';
import Takedown from './index';

export default connect(
	( state ) => {
		return {
			list: state.takedown.list
		};
	},
	( dispatch ) => {
		return {
			onComponentWillMount: () => {
				dispatch( fetchTakedownList() );
			}
		};
	}
)( Takedown );
