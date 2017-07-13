import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import { User } from 'entities/user';
import { defaultUserNoticeText } from 'utils';

export class TakedownCreateDmcaUserNotice extends React.Component {
	updateNotice( value ) {
		const notice = this.props.noticeUser.set( 'notice', value );

		this.props.onChange( this.props.notices.remove( notice ).add( notice ) );
	}

	addNotice() {
		this.props.onChange( this.props.notices.add( this.props.user ) );
	}

	removeNotice() {
		this.props.onChange( this.props.notices.remove( this.props.noticeUser ) );
	}

	getNoticeText() {
		if ( typeof this.props.noticeUser.notice !== 'undefined' ) {
			return this.props.noticeUser.notice;
		}

		return defaultUserNoticeText( this.props.noticeUser.username, this.props.pageIds );
	}

	render() {
		let noticeEditButton,
			noticeEdit;

		if ( this.props.noticeUser ) {
			if ( typeof this.props.noticeUser.notice === 'undefined' ) {
				noticeEditButton = (
					<span className="input-group-btn">
						<button className="btn btn-secondary" type="button" onClick={() => {
							this.updateNotice( defaultUserNoticeText( this.props.noticeUser.username, this.props.pageIds ) );
							this.textInput.focus();
						}}><i className="material-icons">edit</i></button>
					</span>
				);
			}

			noticeEdit = (
				<div className="form-group">
					<label>Text</label>
					<div className="input-group">
						<textarea
							className="form-control"
							rows="5"
							value={this.props.noticeUser.notice || defaultUserNoticeText( this.props.noticeUser.username, this.props.pageIds )}
							readOnly={typeof this.props.noticeUser.notice === 'undefined'}
							ref={( element ) => { this.textInput = element; }}
							onChange={( event ) => {
								this.updateNotice( event.target.value );
							}} />
						{noticeEditButton}
					</div>
				</div>
			);
		}

		return (
			<div>
				<div className="form-group">
					<div className="form-check">
						<label className="form-check-label">
							<input
								disabled={this.props.disabled}
								className="form-check-input"
								type="checkbox"
								checked={!!this.props.noticeUser}
								onChange={ ( event ) => {
									if ( event.target.checked ) {
										this.addNotice();
									} else {
										this.removeNotice();
									}
								} }
							/> Post notice on {this.props.user.username + '\'s'} talk page
						</label>
					</div>
				</div>
				{noticeEdit}
			</div>
		);
	}
}

TakedownCreateDmcaUserNotice.propTypes = {
	user: PropTypes.instanceOf( User ).isRequired,
	noticeUser: PropTypes.instanceOf( User ),
	notices: PropTypes.instanceOf( Set ).isRequired,
	pageIds: PropTypes.instanceOf( Set ).isRequired,
	onChange: PropTypes.func.isRequired,
	disabled: PropTypes.bool
};
