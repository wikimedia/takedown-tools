import { combineEpics } from 'redux-observable';
import { fetchTakedownList } from './takedown';

export default combineEpics(
	fetchTakedownList
);
