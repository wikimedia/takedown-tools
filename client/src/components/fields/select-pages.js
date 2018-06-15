import React from 'react';
import PropTypes from 'prop-types';
import { Creatable } from 'react-select';
import 'react-select/dist/react-select.css';
import { Set } from 'immutable';
import { Title } from 'mediawiki-title';
import { Subject, Observable } from 'rxjs';
import 'rxjs/add/observable/dom/ajax';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/skipWhile';
import 'rxjs/add/operator/distinctUntilChanged';
import 'rxjs/add/operator/debounceTime';
import { Site } from '../../entities/site';

export class SelectPages extends React.Component {

	constructor( props ) {
		super( props );

		this.onChange = this.onChange.bind( this );
		this.onInputChange = this.onInputChange.bind( this );
		this.newOptionCreator = this.newOptionCreator.bind( this );
		this.isOptionUnique = this.isOptionUnique.bind( this );
		this.optionRenderer = this.optionRenderer.bind( this );
		this.textChange = new Subject();

		// This components state should be self contained.
		this.state = {
			value: this.getOptionsFromPageIds( props.value ),
			loading: false
		};

		this.textChange
			// Skip while the site is missing.
			.skipWhile( () => !this.props.site.info )
			.distinctUntilChanged()
			.debounceTime( 250 )
			.switchMap( ( input ) => {
				// Set the loading state.
				this.setState( {
					...this.state,
					loading: true
				} );

				// Query for the users.
				return Observable.ajax( {
					url: 'https://' + this.props.site.domain + '/w/api.php?action=query&format=json&list=search&utf8=1&srnamespace=*&origin=*&srsearch=' + encodeURIComponent( input ),
					crossDomain: true
				} ).map( ( ajaxResponse ) => {
					if ( !ajaxResponse.response.query ) {
						return [];
					}

					if ( !ajaxResponse.response.query.search ) {
						return [];
					}

					return ajaxResponse.response.query.search.map( ( data ) => {
						return this.getOptionFromText( data.title );
					} ).filter( ( option ) => !!option );

				} ).catch( ( e ) => {
					console.error( e );
					return Observable.of( [] );
				} );
			} ).subscribe( ( options ) => {
				// Set the internal state.
				this.setState( {
					...this.state,
					loading: false,
					options: options
				} );
			} );
	}

	componentWillReceiveProps( nextProps ) {
		this.setState( {
			...this.state,
			value: this.getOptionsFromPageIds( nextProps.value )
		} );
	}

	getOptionFromText( text ) {
		if ( !this.props.site.info ) {
			return undefined;
		}

		const title = Title.newFromText( text, this.props.site.info );

		return {
			label: title.getPrefixedText(),
			value: title.getPrefixedDBKey()
		};
	}

	getOptionsFromPageIds( pageIds ) {
		if ( !pageIds ) {
			return [];
		}

		if ( !this.props.site.info ) {
			return [];
		}

		return pageIds.map( ( id ) => this.getOptionFromText( id ) ).filter( ( option ) => !!option ).toArray();
	}

	getPageIdsFromOptions( options ) {
		if ( !options ) {
			return [];
		}

		if ( !this.props.site.info ) {
			return [];
		}

		return options.map( ( option ) => option.value );
	}

	onInputChange( input ) {
		this.textChange.next( input );
		return input;
	}

	onChange( value ) {
		// Set the internal state.
		this.setState( {
			...this.state,
			value: value
		} );

		// Send the value upstream.
		if ( this.props.onChange ) {
			this.props.onChange( new Set( this.getPageIdsFromOptions( value ) ) );
		}
	}

	newOptionCreator( option ) {
		return {
			...option,
			missing: true,
			...this.getOptionFromText( option.label ),
		};
	}

	isOptionUnique({ option, options }) {
		if ( !options ) {
			return true;
		}

		return !options.find(o => o.value === option.value);
	}

	optionRenderer( option ) {
		return (
			<span style={{color: option.missing ? 'red' : 'black'}}>
				{option.label}
			</span>
		);
	}

	render() {
		let disabled = this.props.disabled,
			loading = this.state.loading;

		if ( !this.props.site.id ) {
			disabled = true;
		} else if ( !this.props.site.info ) {
			loading = true;
		}

		return (
			<Creatable
				name={this.props.name}
				disabled={disabled}
				value={this.state.value}
				multi={true}
				isLoading={loading}
				options={this.state.options}
				onInputChange={this.onInputChange}
				onChange={this.onChange}
				filterOption={option => option}
				newOptionCreator={this.newOptionCreator}
				promptTextCreator={label => label}
				isOptionUnique={this.isOptionUnique}
				optionRenderer={this.optionRenderer}
			/>
		);
	}
}

SelectPages.propTypes = {
	name: PropTypes.string.isRequired,
	site: PropTypes.instanceOf( Site ),
	onChange: PropTypes.func,
	disabled: PropTypes.bool,
	value: PropTypes.instanceOf( Set )
};
