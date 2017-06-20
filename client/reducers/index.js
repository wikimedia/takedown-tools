import { combineReducers } from 'redux';
import { routerReducer } from 'react-router-redux';
import takedown from './takedown';
import token from './token';

export default combineReducers( {
	token,
	takedown,
	router: routerReducer
} );
