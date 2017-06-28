import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';
import { SingleDatePicker } from 'react-dates';
import 'react-dates/lib/css/_datepicker.css';

export class DatePicker extends React.Component {

	constructor( props ) {
		super( props );

		// UI state can be maintained within the component.
		this.state = {
			focused: false
		};
	}

	onFocusChange( state ) {
		this.setState( {
			...this.state,
			...state
		} );
	}

	onDateChange( date ) {
		if ( !date ) {
			this.props.onChange();
			return;
		}

		this.props.onChange( date.utc().toISOString() );
	}

	isOutsideRange( date ) {
		return date.diff( moment() ) > 0;
	}

	initialVisibleMonth() {
		return moment().subtract( 1, 'month' );
	}

	render() {
		let value;

		if ( this.props.value ) {
			value = moment.utc( this.props.value );
		}

		return (
			<div className="row">
				<div className="col">
					<SingleDatePicker
						date={value}
						onDateChange={this.onDateChange.bind( this )}
						focused={this.state.focused}
						onFocusChange={this.onFocusChange.bind( this )}
						isOutsideRange={this.isOutsideRange.bind( this )}
						initialVisibleMonth={this.initialVisibleMonth.bind( this )}
						showClearDate
						showDefaultInputIcon
						hideKeyboardShortcutsPanel
					/>
				</div>
			</div>
		);
	}
}

DatePicker.propTypes = {
	value: PropTypes.string,
	onChange: PropTypes.func.isRequired
};
