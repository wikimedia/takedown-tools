export function fetchAll() {
	return {
		type: 'SITE_FETCH_ALL'
	};
}

export function addMultiple( sites ) {
	return {
		type: 'SITE_ADD_MULTIPLE',
		sites: sites
	};
}

export function add( site ) {
	return {
		type: 'SITE_ADD',
		site: site
	};
}
