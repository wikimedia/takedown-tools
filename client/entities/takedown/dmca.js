import { Record, Set, List } from 'immutable';

export class Dmca extends Record( {
	sendCe: undefined,
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
	pageIds: new Set()
} ) {
	constructor( data = {} ) {
		data = {
			...data,
			senderAddress: new List( data.senderAddress ? data.senderAddress : [] ),
			pageIds: new Set( data.pageIds ? data.pageIds : [] )
		};
		super( data );
	}

}
