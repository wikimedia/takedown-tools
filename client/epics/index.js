import { combineEpics } from 'redux-observable';
import * as TakedownEpic from './takedown';
import * as FileEpic from './file';
import * as UserEpic from './user';
import * as SiteEpic from './site';
import * as TokenEpic from './token';

export default combineEpics(
	FileEpic.upload,
	FileEpic.deleteFile,
	FileEpic.fetchFiles,
	FileEpic.readExif,
	TokenEpic.refreshToken,
	SiteEpic.fetchAll,
	SiteEpic.fetchSiteInfo,
	TakedownEpic.fetchTakedownList,
	TakedownEpic.fetchTakedown,
	TakedownEpic.takedownSave,
	TakedownEpic.saveDmcaPost,
	UserEpic.fetchUsers
);
