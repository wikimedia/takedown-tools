import { createSelector } from 'reselect';
import { User, Site } from '../entity';

export function makeGetTakedown() {
	return createSelector(
		state => state.takedown.list,
		( _, props ) => parseInt( props.match.params.id ),
		( takedowns, id ) => {
			return takedowns.find( ( takedown ) => {
				return takedown.id === id;
			} );
		}
	);
}

export function makeGetInvolved() {
	return createSelector(
		state => state.user.list,
		( _, props ) => props.takedown ? props.takedown.involvedIds : [],
		( users, involvedIds ) => {
			return involvedIds.map( ( id ) => {
				return users.find( ( user ) => {
					return user.id === id;
				} );
			} ).filter( ( user ) => {
				return typeof user !== 'undefined';
			} );
		}
	);
}

export function makeGetReporter() {
	return createSelector(
		state => state.user.list,
		( _, props ) => props.takedown ? props.takedown.reporterId : undefined,
		( users, reporterId ) => {
			if ( !reporterId ) {
				return new User();
			}

			let reporter = users.find( ( user ) => {
				return user.id === reporterId;
			} );

			if ( !reporter ) {
				reporter = new User();
			}

			return reporter;
		}
	);
}

export function makeGetTakedownList() {
	return createSelector(
		state => state.takedown.list,
		( takedowns ) => {
			return takedowns.filter( ( takedown ) => {
				return !takedown.error;
			} );
		}
	);
}

export function makeGetSite() {
	return createSelector(
		state => state.site.list,
		( _, props ) => props.takedown ? props.takedown.siteId : undefined,
		( sites, siteId ) => {
			if ( !siteId ) {
				return new Site();
			}

			let site = sites.find( ( site ) => {
				return site.id === siteId;
			} );

			if ( !site ) {
				site = new Site();
			}

			return site;
		}
	);
}
