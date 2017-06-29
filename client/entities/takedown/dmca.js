import { Record, Set, List } from 'immutable';

export class Dmca extends Record( {
	sendCe: undefined,
	contentTypeIds: new Set(),
	senderName: undefined,
	senderPerson: undefined,
	senderFirm: undefined,
	senderAddress: new List(),
	senderCity: undefined,
	senderState: undefined,
	senderZip: undefined,
	senderCountryCode: undefined,
	sent: undefined,
	actionTakenId: undefined
} ) {
	constructor( data = {} ) {
		data = {
			...data,
			contentTypeIds: new Set( data.contentTypeIds ? data.contentTypeIds : [] ),
			senderAddress: new List( data.senderAddress ? data.senderAddress : [] )
		};
		super( data );
	}

}
