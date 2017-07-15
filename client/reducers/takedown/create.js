import { Takedown } from 'app/entities/takedown/takedown';

export default function create( state = new Takedown(), action ) {
	switch ( action.type ) {
		case 'TAKEDOWN_CREATE_SAVE':
			return state.set( 'status', 'saving' );
		case 'TAKEDOWN_CREATE_UPDATE':
			return action.takedown;
		case 'TAKEDOWN_CREATE_CLEAR':
			return new Takedown();
		default:
			return state;
	}
}
