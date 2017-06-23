import { combineReducers } from 'redux';
import { routerReducer } from 'react-router-redux';
import takedown from './takedown';
import site from './site';
import token from './token';
import user from './user';

export default combineReducers( {
	router: routerReducer,
	takedown,
	token,
	site,
	user
} );
