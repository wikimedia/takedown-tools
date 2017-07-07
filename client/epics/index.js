import { combineEpics } from 'redux-observable';
import * as TakedownEpic from './takedown';
import * as FileEpic from './file';
import * as UserEpic from './user';
import * as SiteEpic from './site';
import * as TokenEpic from './token';

export default combineEpics(
	FileEpic.upload,
	TokenEpic.refreshToken,
	SiteEpic.fetchAll,
	SiteEpic.fetchSiteInfo,
	TakedownEpic.fetchTakedownList,
	TakedownEpic.fetchTakedown,
	TakedownEpic.takedownSave,
	UserEpic.fetchUsers
);
