import React from 'react';
import { HeaderContainer } from './header';
import { TakedownIndexContainer } from './takedown/index/index';
import { TakedownShowContainer } from './takedown/show';
import { TakedownCreateContainer } from './takedown/create/create';
import { Switch, Route } from 'react-router-dom';

export default class App extends React.Component {
	render() {
		return (
			<div className="container">
				<HeaderContainer />
				<Switch>
					<Route exact path="/" component={TakedownIndexContainer} />
					<Route path="/takedown/create" component={TakedownCreateContainer} />
					<Route path="/takedown/:id" component={TakedownShowContainer} />
				</Switch>
			</div>
		);
	}
}
