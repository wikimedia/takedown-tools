import { combineReducers } from 'redux';
import list from './list.js';
import status from './status.js';

export default combineReducers( {
	list,
	status
} );
