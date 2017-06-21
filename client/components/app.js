import React from 'react';
import HeaderContainer from './header/container';
import TakedownContainer from './takedown/container';
import TakedownViewContainer from './takedown/view/container';
import { Route } from 'react-router-dom';

export default class App extends React.Component {
	render() {
		return (
			<div className="container">
				<HeaderContainer />
				<Route exact path="/" component={TakedownContainer} />
				<Route path="/takedown/:id" component={TakedownViewContainer} />
			</div>
		);
	}
}
