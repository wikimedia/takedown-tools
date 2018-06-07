import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/distinct';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/first';
import 'rxjs/add/operator/publishReplay';
import 'rxjs/add/operator/delayWhen';
import 'rxjs/add/operator/bufferWhen';
import 'rxjs/add/observable/dom/ajax';
import 'rxjs/add/observable/from';
import { Site } from '../entities/site';
import * as SiteActions from '../actions/site';
import QueryString from 'querystring';

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

export function fetchSiteInfroFromTakedown( action$, store ) {
	return action$
		// Skip until the sites have been added.
		.skipUntil( action$.ofType( 'SITE_ADD_MULTIPLE' ) )
		.filter( ( action ) => {
			const types = [
				'TAKEDOWN_ADD_MULTIPLE',
				'TAKEDOWN_ADD',
				'TAKEDOWN_CREATE_UPDATE',
				'SITE_ADD_MULTIPLE'
			];

			return types.includes( action.type );
		} )
		.flatMap( ( action ) => {
			if ( action.type === 'SITE_ADD_MULTIPLE' ) {
				return [
					...store.getState().takedown.list.toArray(),
					store.getState().takedown.create
				];
			}
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
		.map( ( takedown ) => {
			return store.getState().site.list.find( ( site ) => {
				return site.id === takedown.siteId;
			} );
		} )
		.flatMap( ( site ) => {
			return Observable.of( SiteActions.fetchInfo( site ) );
		} );
}

export function fetchFoundationSiteInfro( action$, store ) {
	return action$
		// Skip until the sites have been added.
		.skipUntil( action$.ofType( 'SITE_ADD_MULTIPLE' ) )
		.ofType( 'TAKEDOWN_CREATE_UPDATE' )
		.filter( ( action ) => action.takedown.type === 'dmca' && action.takedown.pageIds.size > 0 && action.takedown.dmca.wmfSend )
		.first()
		.flatMap( () => {
			const site = store.getState().site.list.find( ( item ) => {
				return item.id === 'foundationwiki';
			} );

			return Observable.of( SiteActions.fetchInfo( site ) );
		} );
}

export function fetchSiteInfo( action$ ) {
	return action$.ofType( 'SITE_FETCH_INFO' ).flatMap( ( action ) => {
		const query = {
			action: 'query',
			format: 'json',
			meta: 'siteinfo',
			siprop: 'general|namespaces|namespacealiases|specialpagealiases|interwikimap',
			origin: '*'
		};

		return Observable.ajax( {
			url: 'https://' + action.site.domain + '/w/api.php?' + QueryString.stringify( query ),
			crossDomain: true,
			responseType: 'json'
		} ).map( ( ajaxResponse ) => {
			return SiteActions.update( action.site.set( 'info', ajaxResponse.response.query ) );
		} ).catch( ( ajaxError ) => {
			return Observable.of( SiteActions.update( action.site.set( 'error', ajaxError.status ) ) );
		} );
	} );
}
