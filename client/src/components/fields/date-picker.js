import React from 'react';
import PropTypes from 'prop-types';
import TimePicker from 'rc-time-picker';
import moment from 'moment';
import { SingleDatePicker } from 'react-dates';
import 'react-dates/lib/css/_datepicker.css';
import 'rc-time-picker/assets/index.css';

export class DatePicker extends React.Component {

	constructor( props ) {
		super( props );

		this.eod = moment.utc().endOf( 'day' );

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

		const value = this.props.value ? moment.utc( this.props.value ) : moment.utc();
		value.set( {
			year: date.utc().year(),
			month: date.utc().month(),
			date: date.utc().date()
		} );

		this.props.onChange( value.utc().toISOString() );
	}

	isOutsideRange( date ) {
		return date.diff( this.eod ) > 0;
	}

	initialVisibleMonth() {
		return moment.utc().subtract( 1, 'month' );
	}

	onTimeChange( time ) {
		if ( !time ) {
			this.props.onChange();
			return;
		}

		const value = this.props.value ? moment.utc( this.props.value ) : moment.utc();
		value.set( {
			hours: time.utc().hours(),
			minutes: time.utc().minutes()
		} );

		this.props.onChange( value.utc().toISOString() );
	}

	render() {
		let value,
			timePicker;

		if ( this.props.value ) {
			value = moment.utc( this.props.value );
		}

		if ( this.props.time ) {
			timePicker = (
				<div>
					<TimePicker
						disabled={this.props.disabled}
						value={value ? value.local() : undefined}
						onChange={this.onTimeChange.bind( this )}
						showSecond={false}
						placeholder="Time"
					/>
				</div>
			);
		}

		return (
			<div>
				<div className="d-flex align-items-center">
					<div>
						<SingleDatePicker
							date={value}
							onDateChange={this.onDateChange.bind( this )}
							focused={this.state.focused}
							onFocusChange={this.onFocusChange.bind( this )}
							isOutsideRange={this.isOutsideRange.bind( this )}
							initialVisibleMonth={this.initialVisibleMonth.bind( this )}
							disabled={this.props.disabled}
							showClearDate
							showDefaultInputIcon
							hideKeyboardShortcutsPanel
						/>
					</div>
					{timePicker}
				</div>
			</div>
		);
	}
}

DatePicker.propTypes = {
	value: PropTypes.string,
	disabled: PropTypes.bool,
	time: PropTypes.bool,
	onChange: PropTypes.func.isRequired
};
