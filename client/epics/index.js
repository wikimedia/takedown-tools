import { combineEpics } from 'redux-observable';
import * as TakedownEpic from './takedown';
import * as UserEpic from './user';

export default combineEpics(
	TakedownEpic.fetchTakedownList,
	TakedownEpic.fetchTakedown,
	UserEpic.fetchUsers
);
