import { Record } from 'immutable';

export class Post extends Record( {
	title: undefined,
	text: undefined,
	status: 'clean'
} ) {}
