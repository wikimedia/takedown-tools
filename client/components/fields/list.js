import React from 'react';
import PropTypes from 'prop-types';
import { OrderedMap } from 'immutable';

export class ListField extends React.Component {
	constructor( props ) {
		super( props );

		this.state = {
			next: props.value ? props.value.size : 0
		};
	}

	addItem() {
		if ( !this.props.onChange ) {
			return;
		}

		this.props.onChange( this.props.value.set( this.state.next, '' ) );

		this.setState( {
			...this.state,
			next: this.state.next + 1
		} );
	}

	updateItem( key, value ) {
		if ( !this.props.onChange ) {
			return;
		}

		this.props.onChange( this.props.value.set( key, value ) );
	}

	removeItem( key ) {
		if ( !this.props.onChange ) {
			return;
		}

		this.props.onChange( this.props.value.delete( key ) );
	}

	render() {
		const fields = this.props.value.map( ( item, key ) => {
			const name = `${this.props.name} [${key}]`;

			return (
				<div className="input-group mb-2" key={key}>
					<input className="form-control" type={this.props.type} name={name} disabled={this.props.disabled} onChange={( event ) => this.updateItem( key, event.target.value )} />
					<span className="input-group-btn">
						<button className="btn btn-secondary" type="button" onClick={() => this.removeItem( key )}>×</button>
					</span>
				</div>
			);
		} ).toArray();

		return (
			<div>
				{fields}
				<div>
					<button type="button" className="btn btn-secondary btn-sm" onClick={this.addItem.bind( this )}>+ Add</button>
				</div>
			</div>
		);
	}
}

ListField.propTypes = {
	value: PropTypes.instanceOf( OrderedMap ).isRequired,
	type: PropTypes.string,
	name: PropTypes.string,
	disabled: PropTypes.bool,
	onChange: PropTypes.func
};
