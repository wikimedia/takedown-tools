import { Set } from 'immutable';
import { Metadata } from './metadata';

export const MetadataSet = new Set( [
	new Metadata( {
		id: 'checkuser',
		label: 'Checkuser data was available and is being included below.',
		type: 'cp'
	} ),
	new Metadata( {
		id: 'email-request',
		label: 'An email was sent to legal@rt.wikimedia.org with the file name asking for it to be deleted.',
		type: 'cp'
	} ),
	new Metadata( {
		id: 'taken-down-apparent',
		label: 'The content was taken down and we have awareness of facts or circumstances from which infringing activity is apparent.',
		type: 'dmca'
	} ),
	new Metadata( {
		id: 'taken-down-dmca',
		label: 'The content was taken down pursuant to a DMCA notice.',
		type: 'dmca'
	} ),
	new Metadata( {
		id: 'taken-down-infringing',
		label: 'The content was taken down and we have actual knowledge that the content was infringing copyright ',
		type: 'dmca'
	} ),
	new Metadata( {
		id: 'taken-down-suppressed',
		label: 'The content was taken down and suppressed.',
		type: 'cp'
	} ),
	new Metadata( {
		id: 'taken-down-user-warned',
		label: 'The content was taken down and the user was clearly warned and discouraged from future violations.',
		type: 'dmca'
	} ),
	new Metadata( {
		id: 'user-locked',
		label: 'The user who uploaded the content has been locked.',
		type: 'cp'
	} )
] );
