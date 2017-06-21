import React from 'react';
import { HeaderContainer } from './header';
import { TakedownIndexContainer } from './takedown/index';
import { TakedownShowContainer } from './takedown/show';
import { Route } from 'react-router-dom';

export default class App extends React.Component {
	render() {
		return (
			<div className="container">
				<HeaderContainer />
				<Route exact path="/" component={TakedownIndexContainer} />
				<Route path="/takedown/:id" component={TakedownShowContainer} />
			</div>
		);
	}
}
