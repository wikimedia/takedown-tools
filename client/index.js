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
			{
				token: token
			},
			composeWithDevTools( applyMiddleware( router, epicMiddleware ) )
		);

	// If no token is available, redirect to login.
	if ( !token ) {
		window.location = '/login';
		return;
	}

	// Now you can dispatch navigation actions from anywhere!
	// store.dispatch(push('/foo'))
	ReactDOM.render(
		<Provider store={store}>
			<ConnectedRouter history={history}>
				<App name="David" />
			</ConnectedRouter>
		</Provider>,
		document.getElementById( 'root' )
	);
}

// Engage!
main();
