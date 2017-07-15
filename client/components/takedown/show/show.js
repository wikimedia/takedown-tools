import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import moment from 'moment';
import { Takedown } from 'app/entities/takedown/takedown';
import { User } from 'app/entities/user';
import { Site } from 'app/entities/site';
import { Loading } from 'app/components/loading';
import { Error } from 'app/components/error';
import { TakedownShowDmcaContainer } from './dmca/dmca.container';
import { TakedownShowCp } from './cp';

export class TakedownShow extends React.Component {
	componentWillMount() {
		if ( !this.props.takedown && this.props.status !== 'done' ) {
			this.props.fetchTakedown();
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
			const id = 'User:' + user.username.replace( / /g, '_' );

			let username = user.username;

			if ( this.props.site && this.props.site.info ) {
				username = (
					<a href={'https://' + this.props.site.domain + id.replace( /^(.*)$/, this.props.site.info.general.articlepath )}>
						{user.username}
					</a>
				);
			}

			return (
				<div key={user.id}>{username}</div>
			);
		} );

		let site,
			type,
			metadata,
			typeShow,
			created;

		if ( this.props.site.id ) {
			site = (
				<a href={'https://' + this.props.site.domain}>
					{this.props.site.name}
				</a>
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
						<TakedownShowDmcaContainer takedown={this.props.takedown} site={this.props.site} />
					);
					break;
				case 'cp':
					type = 'Child Protection';
					typeShow = (
						<TakedownShowCp takedown={this.props.takedown} />
					);
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
	fetchTakedown: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ),
	metadata: PropTypes.instanceOf( Set ),
	involved: PropTypes.arrayOf( PropTypes.instanceOf( User ) ),
	reporter: PropTypes.instanceOf( User ),
	site: PropTypes.instanceOf( Site )
};
