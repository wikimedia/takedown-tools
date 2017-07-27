import React from 'react';
import ReactDOM from 'react-dom';
import { createStore, applyMiddleware } from 'redux';
import { Provider } from 'react-redux';
import createHistory from 'history/createBrowserHistory';
import { ConnectedRouter, routerMiddleware, replace } from 'react-router-redux';
import App from './components/app';
import reducer from './reducers/index';
import { createEpicMiddleware } from 'redux-observable';
import { composeWithDevTools } from 'redux-devtools-extension';
import epic from './epics';
import './styles/styles.scss';
import querystring from 'querystring';

function main() {
	const token = window.localStorage.getItem( 'token' ),
		// Create a history of your choosing (we're using a browser history in this case)
		history = createHistory(),
		// Build the middleware for intercepting and dispatching navigation actions
		router = routerMiddleware( history ),
		epicMiddleware = createEpicMiddleware( epic ),
		// Add the reducer to your store on the `router` key
		// Also apply our middleware for navigating
		store = createStore(
			reducer,
			composeWithDevTools( applyMiddleware( router, epicMiddleware ) )
		),
		query = querystring.parse( window.location.search.replace( '?', '' ) );

	if ( query.token ) {
		// Add the token to the store.
		store.dispatch( {
			type: 'TOKEN_ADD',
			token: query.token
		} );

		// Remove the token from the url.
		store.dispatch( replace( window.location.pathname ) );
	} else if ( token ) {
		// Add the token to the store.
		store.dispatch( {
			type: 'TOKEN_ADD',
			token: token
		} );
	} else {
		// If no token is available, redirect to login.
		window.location = '/login';
		return;
	}

	ReactDOM.render(
		<Provider store={store}>
			<ConnectedRouter history={history}>
				<App />
			</ConnectedRouter>
		</Provider>,
		document.getElementById( 'root' )
	);
}

// Engage!
main();
