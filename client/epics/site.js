import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/distinct';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/first';
import 'rxjs/add/operator/delayWhen';
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

export function fetchSiteInfo( action$, store ) {
	return action$
		.filter( ( action ) => {
			const types = [
				'TAKEDOWN_ADD_MULTIPLE',
				'TAKEDOWN_ADD',
				'TAKEDOWN_CREATE_UPDATE'
			];

			return types.includes( action.type );
		} )
		.flatMap( ( action ) => {
			if ( action.type === 'TAKEDOWN_ADD_MULTIPLE' ) {
				return [
					...action.takedowns
				];
			}

			return [ action.takedown ];
		} )
		// Continue if takedown has a site id.
		.filter( ( takedown ) => !!takedown.siteId )
		// Only do this once per siteId
		.distinct( ( takedown ) => takedown.siteId )
		// Delay the emmissions until the sites are added.
		.combineLatest( action$.ofType( 'SITE_ADD_MULTIPLE' ), ( takedown ) => takedown )
		.map( ( takedown ) => {
			return store.getState().site.list.find( ( site ) => {
				return site.id === takedown.siteId;
			} );
		} )
		.flatMap( ( site ) => {
			return Observable.ajax( {
				url: 'https://' + site.domain + '/w/api.php?action=query&format=json&meta=siteinfo&siprop=general%7Cnamespaces%7Cnamespacealiases%7Cspecialpagealiases&origin=*',
				crossDomain: true,
				responseType: 'json'
			} )
				.map( ( ajaxResponse ) => {
					return SiteActions.update( site.set( 'info', ajaxResponse.response.query ) );
				} )
				.catch( ( ajaxError ) => {
					return Observable.of( SiteActions.update( site.set( 'error', ajaxError.status ) ) );
				} );
		} );

}
