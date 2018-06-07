import { Set } from 'immutable';
import { Country } from './country';

export const countries = require( '../../data/countries' ).map( ( data ) => {
		return new Country( {
			id: data[ 'alpha-2' ],
			name: data.name
		} );
	} ),
	CountrySet = new Set( countries ).sortBy( country => country.name );
