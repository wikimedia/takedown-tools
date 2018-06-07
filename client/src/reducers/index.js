import { combineReducers } from 'redux';
import { routerReducer } from 'react-router-redux';
import takedown from './takedown';
import file from './file';
import site from './site';
import token from './token';
import user from './user';

export default combineReducers( {
	router: routerReducer,
	file,
	takedown,
	token,
	site,
	user
} );
