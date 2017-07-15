import React from 'react';
import PropTypes from 'prop-types';
import { TextEdit } from 'app/components/fields/text-edit';
import { Submit } from 'app/components/fields/submit';
import { Takedown } from 'app/entities/takedown/takedown';
import { Site } from 'app/entities/site';
import { defaultCommonsText, defaultCommonsVillagePumpText } from 'app/utils';

export class TakedownShowDmcaCommonsPost extends React.Component {

	updateField( fieldName, value ) {
		const takedown = this.props.takedown.setIn( [ 'dmca', this.props.postName, fieldName ], value )
			.setIn( [ 'dmca', this.props.postName, 'status' ], 'dirty' );

		this.props.updateTakedown( takedown );
	}

	getCommonsText() {
		if ( typeof this.props.takedown.dmca[ this.props.postName ].text !== 'undefined' ) {
			return this.props.takedown.dmca[ this.props.postName ].text;
		}

		return this.getDefaultCommonsText();
	}

	getDefaultCommonsText() {
		switch ( this.props.postName ) {
			case 'commonsPost':
				return defaultCommonsText( this.getCommonsTitle(), this.props.takedown.dmca.wmfTitle, this.props.takedown.dmca.pageIds );
			case 'commonsVillagePumpPost':
				return defaultCommonsVillagePumpText( this.getCommonsTitle(), this.props.takedown.dmca.wmfTitle, this.props.takedown.dmca.pageIds );
		}

		return '';
	}

	getCommonsTitle() {
		return this.props.takedown.dmca[ this.props.postName ].title || this.getDefaultCommonsTitle();
	}

	getDefaultCommonsTitle() {
		return this.getWmfTitle() || '';
	}

	getWmfTitle() {
		return this.props.takedown.dmca.wmfTitle ? this.props.takedown.dmca.wmfTitle.replace( /_/g, ' ' ) : undefined;
	}

	handleSubmit( event ) {
		event.preventDefault();

		const post = this.props.takedown.dmca[ this.props.postName ]
			.set( 'title', this.getCommonsTitle() )
			.set( 'text', this.getCommonsText() );

		this.props.savePost( this.props.takedown, this.props.postName, post );
	}

	render() {
		let disabled,
			name,
			send,
			verb,
			text,
			post;

		if ( !this.props.site || this.props.site.id !== 'commonswiki' ) {
			return null;
		}

		post = this.props.takedown.dmca[ this.props.postName ];
		disabled = post.status === 'saving';

		switch ( this.props.postName ) {
			case 'commonsPost':
				send = this.props.takedown.dmca.commonsSend;
				name = (
					<a href="https://commons.wikimedia.org/wiki/Commons:Office_actions/DMCA_notices">Commons</a>
				);
				break;
			case 'commonsVillagePumpPost':
				send = this.props.takedown.dmca.commonsVillagePumpSend;
				name = (
					<a href="https://commons.wikimedia.org/wiki/Commons:Village_pump">Commons Village Pump</a>
				);
				break;
		}

		if ( send ) {
			verb = 'Posted';
			text = 'Yes';
		} else {
			verb = 'Post';

			text = (
				<form onSubmit={this.handleSubmit.bind( this )}>
					<div className="form-group">
						<label htmlFor="title">Title</label>
						<TextEdit
							value={post.title}
							default={this.getDefaultCommonsTitle()}
							name="title"
							disabled={disabled}
							onChange={( value ) => this.updateField( 'title', value )} />
					</div>
					<div className="form-group">
						<label htmlFor="text">Text</label>
						<TextEdit
							rows="5"
							value={post.text}
							default={this.getDefaultCommonsText()}
							name="text"
							disabled={disabled}
							onChange={( value ) => this.updateField( 'text', value )} />
					</div>
					<Submit status={post.status === 'clean' ? 'dirty' : post.status} value="Post" />
				</form>
			);
		}

		return (
			<tr>
				<td>
					{verb} to {name}
				</td>
				<td>
					{text}
				</td>
			</tr>
		);
	}
}

TakedownShowDmcaCommonsPost.propTypes = {
	takedown: PropTypes.instanceOf( Takedown ),
	site: PropTypes.instanceOf( Site ),
	postName: PropTypes.string.isRequired,
	savePost: PropTypes.func.isRequired,
	updateTakedown: PropTypes.func.isRequired
};
