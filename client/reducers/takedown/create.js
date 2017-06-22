import { Takedown } from '../../entity';

export default function create( state = new Takedown(), action ) {
	switch ( action.type ) {
		case 'TAKEDOWN_CREATE_SAVE':
			return state.set( 'status', 'saving' );
		case 'TAKEDOWN_CREATE_UPDATE':
			return action.takedown;
		default:
			return state;
	}
}
