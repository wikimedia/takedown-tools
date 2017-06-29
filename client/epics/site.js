import { Observable } from 'rxjs';
import 'rxjs/add/operator/switchMap';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/first';
import 'rxjs/add/observable/dom/ajax';
import 'rxjs/add/observable/from';
import { Site } from '../entities/site';
import * as SiteActions from '../actions/site';

export function fetchAll( action$ ) {
	return action$
		.filter( ( action ) => {
			const types = [
				'TAKEDOWN_ADD_MULTIPLE',
				'TAKEDOWN_ADD',
				'SITE_FETCH_ALL'
			];

			return types.includes( action.type );
		} )
		.first()
		.flatMap( () => {
			return Observable.ajax( {
				url: 'https://meta.wikimedia.org/w/api.php?action=sitematrix&format=json&origin=*',
				crossDomain: true,
				responseType: 'json'
			} ).map( ( ajaxResponse ) => {
				const sites = Object.keys( ajaxResponse.response.sitematrix ).filter( ( key ) => {
					return ( !isNaN( parseFloat( key ) ) && isFinite( key ) ) || key === 'specials';
				} )
					.map( ( key ) => {
						// Special is different for some reason.
						if ( key === 'specials' ) {
							return {
								site: ajaxResponse.response.sitematrix[ key ]
							};
						}

						return ajaxResponse.response.sitematrix[ key ];
					} )
					.reduce( ( state, data ) => {
						return [
							...state,
							...data.site.map( ( item ) => {
								return new Site( {
									id: item.dbname,
									name: item.sitename,
									domain: new URL( item.url ).hostname,
									projectId: item.code
								} );
							} )
						];
					}, [] );

				return SiteActions.addMultiple( sites );
			} )
				.catch( () => {
					return Observable.of( SiteActions.addMultiple( [] ) );
				} );
		} );
}
