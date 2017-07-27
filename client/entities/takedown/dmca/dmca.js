import { Record, Set, List, Map, fromJS } from 'immutable';
import { Post } from './post';

export class Dmca extends Record( {
	lumenSend: undefined,
	lumenId: undefined,
	lumenTitle: undefined,
	senderName: undefined,
	senderPerson: undefined,
	senderFirm: undefined,
	senderAddress: new List(),
	senderCity: undefined,
	senderState: undefined,
	senderZip: undefined,
	senderCountryCode: undefined,
	sent: undefined,
	actionTakenId: undefined,
	originalUrls: new List(),
	method: undefined,
	subject: undefined,
	body: undefined,
	fileIds: new List(),
	wmfSend: undefined,
	wmfTitle: undefined,
	commonsSend: undefined,
	commonsPost: new Post(),
	commonsVillagePumpSend: undefined,
	commonsVillagePumpPost: new Post(),
	userNoticeIds: new Set(),
	notices: new Map()
} ) {
	constructor( data = {} ) {
		data = {
			...data,
			senderAddress: new List( data.senderAddress || [] ),
			originalUrls: fromJS( data.originalUrls || [] ).toOrderedMap(),
			fileIds: fromJS( data.fileIds || [] ).toList(),
			commonsPost: new Post( data.commonsPost || {} ),
			commonsVillagePumpPost: new Post( data.commonsVillagePumpPost || {} ),
			userNoticeIds: new Set( data.userNoticeIds || [] ),
			notices: new Map( data.notices || [] )
		};
		super( data );
	}

}
