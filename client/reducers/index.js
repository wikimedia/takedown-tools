import { combineReducers } from 'redux';
import { routerReducer } from 'react-router-redux';
import takedown from './takedown';
import token from './token';
import user from './user';

export default combineReducers( {
	token,
	takedown,
	user,
	router: routerReducer
} );
