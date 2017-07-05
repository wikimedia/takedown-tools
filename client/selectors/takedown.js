import { createSelector } from 'reselect';
import { Set } from 'immutable';
import { User } from '../entities/user';
import { Site } from '../entities/site';
import { MetadataSet } from '../entities/metadata.set';

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

export function makeGetApprover() {
	return createSelector(
		state => state.user.list,
		( _, props ) => props.takedown && props.takedown.cp && props.takedown.cp.approverId ? props.takedown.cp.approverId : undefined,
		( users, id ) => {
			if ( !id ) {
				return undefined;
			}

			return users.find( ( user ) => {
				return user.id === id;
			} );
		}
	);
}

export function makeGetMetadata() {
	return createSelector(
		( _, props ) => props.takedown ? props.takedown.metadataIds : new Set(),
		( metadataIds ) => {
			return metadataIds.map( ( id ) => {
				return MetadataSet.find( ( metadata ) => {
					return metadata.id === id;
				} );
			} ).filter( ( metadata ) => {
				return !!metadata;
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
