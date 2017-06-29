import { Set } from 'immutable';
import { ContentType } from './content-type';

export const ContentTypeSet = new Set( [
	new ContentType( {
		id: 'file',
		label: 'File/Image'
	} ),
	new ContentType( {
		id: 'text',
		label: 'Text'
	} )
] );
