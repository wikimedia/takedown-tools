import React from 'react';
import { HeaderContainer } from './header.container';
import { TakedownIndexContainer } from './takedown/index/index.container';
import { TakedownShowContainer } from './takedown/show/show.container';
import { TakedownCreateContainer } from './takedown/create/create.container';
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
