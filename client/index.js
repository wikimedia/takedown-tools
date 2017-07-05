import React from 'react';
import ReactDOM from 'react-dom';
import { createStore, applyMiddleware } from 'redux';
import { Provider } from 'react-redux';
import createHistory from 'history/createBrowserHistory';
import { ConnectedRouter, routerMiddleware } from 'react-router-redux';
import App from './components/app';
import reducer from './reducers/index';
import { createEpicMiddleware } from 'redux-observable';
import { composeWithDevTools } from 'redux-devtools-extension';
import epic from './epics';
import './styles/styles.scss';

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
		);

	// If no token is available, redirect to login.
	if ( !token ) {
		window.location = '/login';
		return;
	}

	// Add the token to the store. If server rendering is enabled, this should
	// move to after render.
	store.dispatch( {
		type: 'TOKEN_ADD',
		token: token
	} );

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
