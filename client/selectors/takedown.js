import { createSelector } from 'reselect';
import { Set, List } from 'immutable';
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

export function makeGetNotices() {
	return createSelector(
		state => state.user.list,
		( _, props ) => props.takedown && props.takedown.dmca ? props.takedown.dmca.userNoticeIds : new Set(),
		( users, userNoticeIds ) => {
			return userNoticeIds.map( ( id ) => {
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

export function makeGetFiles() {
	return createSelector(
		state => state.file.list,
		( _, props ) => props.takedown && props.takedown.dmca && props.takedown.dmca.fileIds ? props.takedown.dmca.fileIds : new List(),
		( files, fileIds ) => {
			return fileIds.map( ( id ) => {
				return files.find( ( file ) => {
					return file.id === id;
				} );
			} ).filter( ( file ) => {
				return typeof file !== 'undefined';
			} ).toSet();
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

			let site = sites.find( ( item ) => {
				return item.id === siteId;
			} );

			if ( !site ) {
				site = new Site();
			}

			return site;
		}
	);
}

const getFoundationSite = createSelector(
	state => state.site.list,
	( sites ) => {
		return sites.find( ( item ) => {
			return item.id === 'foundationwiki';
		} );
	}
);

export function makeGetContentLink() {
	const getSite = makeGetSite();
	return createSelector(
		state => state.takedown.create,
		getSite,
		getFoundationSite,
		( takedown, site, foundation ) => {
			let inter;

			if ( takedown.pageIds.size === 0 ) {
				return;
			}

			// The index of a set is the value, so convert to an array first.
			return takedown.pageIds.toArray().map( ( id, index ) => {
				let title, url, link;

				title = id.replace( /_/g, ' ' );

				if ( !site || !site.info ) {
					return title;
				}

				url = 'https://' + site.domain + id.replace( /^(.*)$/, site.info.general.articlepath );
				link = `[[${url} ${title}]]`;

				if ( !foundation || !foundation.info ) {
					return link;
				}

				if ( index === 0 ) {
					inter = foundation.info.interwikimap.find( ( map ) => {
						return url === id.replace( /^(.*)$/, map.url );
					} );
				}

				if ( !inter ) {
					return link;
				}

				return `[[${inter.prefix}:${title}]]`;
			} ).join( ' ' );
		}
	);
}
