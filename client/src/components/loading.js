import React from 'react';

export class Loading extends React.Component {
	render() {
		return (
			<div className="row">
				<div className="col">
					<div className="sk-folding-cube">
						<div className="sk-cube1 sk-cube"></div>
						<div className="sk-cube2 sk-cube"></div>
						<div className="sk-cube4 sk-cube"></div>
						<div className="sk-cube3 sk-cube"></div>
					</div>
				</div>
			</div>
		);
	}
}
