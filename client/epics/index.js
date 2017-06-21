import { combineEpics } from 'redux-observable';
import * as TakedownEpic from './takedown';

export default combineEpics(
	TakedownEpic.fetchTakedownList,
	TakedownEpic.fetchTakedown
);
