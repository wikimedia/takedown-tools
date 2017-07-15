import { Record } from 'immutable';
import { Captcha } from 'app/entities/captcha';

export class Post extends Record( {
	title: undefined,
	text: undefined,
	status: 'clean',
	error: undefined,
	captcha: new Captcha()
} ) {
	constructor( data = {} ) {
		data = {
			...data,
			captcha: new Captcha( data.captcha )
		};
		super( data );
	}
}
