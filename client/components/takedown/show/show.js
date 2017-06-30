import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import moment from 'moment';
import { Takedown } from '../../../entities/takedown/takedown';
import { User } from '../../../entities/user';
import { Site } from '../../../entities/site';
import { Loading } from '../../loading';
import { Error } from '../../error';
import { TakedownShowDmca } from './dmca';

export class TakedownShow extends React.Component {
	componentWillMount() {
		if ( this.props.status !== 'done' ) {
			this.props.onComponentWillMount();
		}
	}
	render() {
		if ( !this.props.takedown ) {
			if ( this.props.status === 'fetching' ) {
				return (
					<Loading />
				);
			} else if ( this.props.status === 'done' ) {
				return (
					<Error code={404} />
				);
			} else {
				return null;
			}
		}

		if ( this.props.takedown.error ) {
			return (
				<Error code={this.props.takedown.error} />
			);
		}

		const involved = this.props.involved.map( ( user ) => {
			return (
				<div key={user.id}>{user.username}</div>
			);
		} );

		let site,
			type,
			metadata,
			typeShow,
			created;

		if ( this.props.site.id ) {
			site = (
				<span>
					{this.props.site.name} ({this.props.site.domain})
				</span>
			);
		}

		if ( this.props.takedown.created ) {
			created = moment.utc( this.props.takedown.created ).local().format( 'l LT' );
		}

		if ( this.props.takedown.type ) {
			switch ( this.props.takedown.type ) {
				case 'dmca':
					type = 'DMCA';
					typeShow = (
						<TakedownShowDmca takedown={this.props.takedown} site={this.props.site} />
					);
					break;
				case 'cp':
					type = 'Child Protection';
					break;
			}
		}

		if ( this.props.metadata.size > 0 ) {
			metadata = this.props.metadata.map( ( metadata ) => {
				return (
					<div key={metadata.id}>
						{metadata.label}
					</div>
				);
			} ).toArray();
		}

		return (
			<div>
				<div className="row">
					<div className="col">
						<h2>Takedown #{this.props.takedown.id}</h2>
					</div>
				</div>
				<div className="row">
					<div className="col">
						<table className="table table-bordered table-responsive">
							<tbody>
								<tr>
									<td>Type</td>
									<td>{type}</td>
								</tr>
								<tr>
									<td>Reporter</td>
									<td>{this.props.reporter.username}</td>
								</tr>
								<tr>
									<td>Created</td>
									<td>{created}</td>
								</tr>
								<tr>
									<td>Site</td>
									<td>{site}</td>
								</tr>
								<tr>
									<td>Involved Users</td>
									<td>{involved}</td>
								</tr>
								<tr>
									<td>Metadata</td>
									<td>{metadata}</td>
								</tr>
							</tbody>
							{typeShow}
						</table>
					</div>
				</div>
			</div>
		);
	}
}

TakedownShow.propTypes = {
	status: PropTypes.string.isRequired,
	onComponentWillMount: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ),
	metadata: PropTypes.instanceOf( Set ),
	involved: PropTypes.arrayOf( PropTypes.instanceOf( User ) ),
	reporter: PropTypes.instanceOf( User ),
	site: PropTypes.instanceOf( Site )
};
