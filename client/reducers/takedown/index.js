import { combineReducers } from 'redux';
import create from './create.js';
import list from './list.js';
import status from './status.js';

export default combineReducers( {
	list,
	status,
	create
} );
