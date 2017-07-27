import React from 'react';
import PropTypes from 'prop-types';
import { TextEdit } from 'app/components/fields/text-edit';
import { Submit } from 'app/components/fields/submit';
import { FormError } from 'app/components/fields/form-error';
import { CaptchaField } from 'app/components/fields/captcha';
import { User } from 'app/entities/user';
import { Site } from 'app/entities/site';
import { Post } from 'app/entities/takedown/dmca/post';
import { Takedown } from 'app/entities/takedown/takedown';
import { defaultUserNoticeText } from 'app/utils';

export class TakedownShowDmcaUserNotice extends React.Component {

	updateField( fieldName, value ) {
		const notice = this.props.takedown.dmca.notices.get( this.props.user.id ) || new Post(),
			takedown = this.props.takedown.setIn( [ 'dmca', 'notices', this.props.user.id ], notice )
				.setIn( [ 'dmca', 'notices', this.props.user.id, fieldName ], value )
				.setIn( [ 'dmca', 'notices', this.props.user.id, 'status' ], 'dirty' )
				.setIn( [ 'dmca', 'notices', this.props.user.id, 'error' ], undefined );

		this.props.updateTakedown( takedown );
	}

	handleSubmit( event ) {
		event.preventDefault();

		this.props.saveUserNotice( this.props.takedown, this.props.user );
	}

	render() {
		let userNotice,
			id,
			username,
			disabled,
			notice;

		if ( !this.props.site ) {
			return null;
		}

		notice = this.props.takedown.dmca.notices.get( this.props.user.id ) || new Post();
		disabled = notice.status === 'saving';

		userNotice = this.props.takedown.dmca.userNoticeIds.find( ( id ) => {
			return id === this.props.user.id;
		} );

		if ( userNotice ) {
			username = this.props.user.username;
			id = 'User talk:' + username.replace( / /g, '_' );

			if ( this.props.site && this.props.site.info ) {
				username = (
					<a href={'https://' + this.props.site.domain + id.replace( /^(.*)$/, this.props.site.info.general.articlepath )}>
						{this.props.user.username}
					</a>
				);
			}

			return (
				<div>{username}</div>
			);
		}

		return (
			<form onSubmit={this.handleSubmit.bind( this )}>
				<div className="form-group">
					<label htmlFor="text">{this.props.user.username}</label>
					<TextEdit
						rows="5"
						value={notice.text}
						default={defaultUserNoticeText( this.props.user.username, this.props.takedown.pageIds )}
						name="text"
						disabled={disabled}
						onChange={( value ) => this.updateField( 'text', value )} />
				</div>
				<CaptchaField captcha={notice.captcha} onChange={( value ) => this.updateField( 'captcha', value )} />
				<div className="form-group row align-items-center">
					<div className="col-11">
						<FormError error={notice.error} />
					</div>
					<div className="col-1 text-right">
						<Submit status={notice.status === 'clean' ? 'dirty' : notice.status} value="Post" />
					</div>
				</div>
			</form>
		);
	}
}

TakedownShowDmcaUserNotice.propTypes = {
	user: PropTypes.instanceOf( User ).isRequired,
	site: PropTypes.instanceOf( Site ).isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired,
	updateTakedown: PropTypes.func.isRequired,
	saveUserNotice: PropTypes.func.isRequired
};
