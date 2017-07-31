import React from 'react';
import PropTypes from 'prop-types';
import { TextEdit } from 'app/components/fields/text-edit';
import { Submit } from 'app/components/fields/submit';
import { FormGroup } from 'app/components/fields/form-group';
import { FormError } from 'app/components/fields/form-error';
import { CaptchaField } from 'app/components/fields/captcha';
import { Takedown } from 'app/entities/takedown/takedown';
import { Site } from 'app/entities/site';
import { defaultCommonsText, defaultCommonsVillagePumpText, getWmfTitle } from 'app/utils';

export class TakedownShowDmcaCommonsPost extends React.Component {

	updateField( fieldName, value ) {
		const takedown = this.props.takedown.setIn( [ 'dmca', this.props.postName, fieldName ], value )
			.setIn( [ 'dmca', this.props.postName, 'status' ], 'dirty' )
			.setIn( [ 'dmca', this.props.postName, 'error' ], undefined );

		this.props.updateTakedown( takedown );
	}

	getDefaultCommonsText() {
		switch ( this.props.postName ) {
			case 'commonsPost':
				return defaultCommonsText( this.getCommonsTitle(), this.props.takedown.dmca.wmfTitle, this.props.takedown.pageIds );
			case 'commonsVillagePumpPost':
				return defaultCommonsVillagePumpText( this.getCommonsTitle(), this.props.takedown.dmca.wmfTitle, this.props.takedown.pageIds );
		}

		return '';
	}

	getCommonsTitle() {
		if ( typeof this.props.takedown.dmca[ this.props.postName ].title !== 'undefined' ) {
			return this.props.takedown.dmca[ this.props.postName ].title;
		}

		return this.getDefaultCommonsTitle();
	}

	getDefaultCommonsTitle() {
		return this.getWmfTitle() || '';
	}

	getWmfTitle() {
		return this.props.takedown.dmca.wmfTitle ? getWmfTitle( this.props.takedown.dmca.wmfTitle ) : undefined;
	}

	handleSubmit( event ) {
		event.preventDefault();

		this.props.savePost( this.props.takedown, this.props.postName );
	}

	render() {
		let disabled,
			name,
			id,
			page,
			domain,
			text,
			post;

		if ( !this.props.site || this.props.site.id !== 'commonswiki' ) {
			return null;
		}

		post = this.props.takedown.dmca[ this.props.postName ];
		disabled = post.status === 'saving';
		domain = APP_ENV === 'prod' ? 'commons.wikimedia.org' : 'test2.wikipedia.org';

		switch ( this.props.postName ) {
			case 'commonsPost':
				id = this.props.takedown.dmca.commonsId;
				page = APP_ENV === 'prod' ? 'Commons:Office_actions/DMCA_notices' : 'Office_actions/DMCA_notices';
				name = (
					<a href={`https://${domain}/wiki/${page}`}>Commons</a>
				);
				break;
			case 'commonsVillagePumpPost':
				id = this.props.takedown.dmca.commonsVillagePumpId;
				page = APP_ENV === 'prod' ? 'Commons:Village_pump' : 'Wikipedia:Simple_talk';
				name = (
					<a href={`https://${domain}/wiki/${page}?type=revision&diff=${id}`}>Commons Village Pump</a>
				);
				break;
		}

		if ( id ) {
			text = (
				<a href={`https://${domain}/wiki/${page}?type=revision&diff=${id}`}>{id}</a>
			);
		} else {
			text = (
				<form onSubmit={this.handleSubmit.bind( this )}>
					<FormGroup path="title" error={post.error} render={( hasError, className ) => (
						<div>
							<label htmlFor="title">Title</label>
							<TextEdit
								className={className}
								value={post.title}
								default={this.getDefaultCommonsTitle()}
								name="title"
								disabled={disabled}
								onChange={( value ) => this.updateField( 'title', value )}
							/>
						</div>
					)} />
					<FormGroup path="text" error={post.error} render={( hasError, className ) => (
						<div>
							<label htmlFor="text">Text</label>
							<TextEdit
								rows="5"
								className={className}
								value={post.text}
								default={this.getDefaultCommonsText()}
								name="text"
								disabled={disabled}
								onChange={( value ) => this.updateField( 'text', value )}
							/>
						</div>
					)} />
					<CaptchaField captcha={post.captcha} error={post.error} onChange={( value ) => this.updateField( 'captcha', value )} />
					<div className="form-group row align-items-center">
						<div className="col">
							<FormError error={post.error} />
						</div>
						<div className="col col-auto text-right">
							<Submit status={post.status === 'clean' ? 'dirty' : post.status} value="Post" />
						</div>
					</div>
				</form>
			);
		}

		return (
			<tr>
				<td>
					{name}
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
