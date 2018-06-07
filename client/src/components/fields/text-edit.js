import React from 'react';
import PropTypes from 'prop-types';

export class TextEdit extends React.Component {

	handleChange( event ) {
		this.props.onChange( event.target.value );
	}

	render() {
		let button;

		const rows = parseInt( this.props.rows ),
			Input = rows === 1 ? 'input' : 'textarea';

		if ( typeof this.props.value === 'undefined' ) {
			button = (
				<span className="input-group-btn">
					<button className="btn btn-secondary" type="button" onClick={() => {
						this.props.onChange( this.props.default );
						this.input.focus();
					}}><i className="material-icons">edit</i></button>
				</span>
			);
		}

		return (
			<div className="input-group">
				<Input
					type="text"
					rows={rows > 1 ? rows : undefined}
					className={typeof this.props.className !== 'undefined' ? this.props.className : 'form-control'}
					name={this.props.name}
					ref={( element ) => { this.input = element; }}
					value={typeof this.props.value !== 'undefined' ? this.props.value : this.props.default}
					readOnly={typeof this.props.value === 'undefined' && this.props.default}
					onChange={this.handleChange.bind( this )} />
				{button}
			</div>
		);
	}
}

TextEdit.defaultProps = {
	rows: 1,
	disabled: false,
	'default': ''
};

TextEdit.propTypes = {
	value: PropTypes.string,
	'default': PropTypes.string,
	name: PropTypes.string,
	className: PropTypes.string,
	disabled: PropTypes.bool,
	rows: PropTypes.oneOfType( [
		PropTypes.number,
		PropTypes.string
	] ),
	onChange: PropTypes.func.isRequired
};
