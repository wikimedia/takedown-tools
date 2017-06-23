import { combineEpics } from 'redux-observable';
import * as TakedownEpic from './takedown';
import * as UserEpic from './user';
import * as SiteEpic from './site';

export default combineEpics(
	SiteEpic.fetchAll,
	TakedownEpic.fetchTakedownList,
	TakedownEpic.fetchTakedown,
	TakedownEpic.takedownSave,
	UserEpic.fetchUsers
);
