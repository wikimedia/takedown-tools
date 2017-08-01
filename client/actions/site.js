export function fetchAll() {
	return {
		type: 'SITE_FETCH_ALL'
	};
}

export function fetchInfo( site ) {
	return {
		type: 'SITE_FETCH_INFO',
		site: site
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

export function update( site ) {
	return {
		type: 'SITE_UPDATE',
		site: site
	};
}
