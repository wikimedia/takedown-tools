import { createSelector } from 'reselect';
import { Set } from 'immutable';

export const getSiteOptions = createSelector(
	state => state.site.list,
	( sites = new Set() ) => {
		return sites.map( ( site ) => {
			return {
				value: site.id,
				label: `${site.name} (${site.domain})`
			};
		} ).toArray();
	}
);
