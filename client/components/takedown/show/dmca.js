import React from 'react';
import PropTypes from 'prop-types';
import { Takedown, ContentTypeSet } from '../../../entity';

export class TakedownShowDmca extends React.Component {
	render() {
		let contentTypes;

		if ( this.props.takedown.dmca.contentTypeIds.size > 0 ) {
			contentTypes = this.props.takedown.dmca.contentTypeIds.map( ( id ) => {
				return ContentTypeSet.find( ( contentType ) => {
					return id === contentType.id;
				} );
			} ).filter( ( contentType ) => !!contentType )
				.map( ( contentType ) => {
					return (
						<div key={contentType.id}>
							{contentType.label}
						</div>
					);
				} ).toArray();
		}
		return (
			<div>
				<div className="row pb-2">
					<div className="col-3">
						<strong>Sent to Chilling Effects</strong>
					</div>
					<div className="col-9">
						{this.props.takedown.dmca.ceSend ? 'Yes' : 'No'}
					</div>
				</div>
				<div className="row pb-2">
					<div className="col-3">
						<strong>Content Types</strong>
					</div>
					<div className="col-9">
						{contentTypes}
					</div>
				</div>
			</div>
		);
	}
}

TakedownShowDmca.propTypes = {
	takedown: PropTypes.instanceOf( Takedown )
};
