<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedEventListener {

	/**
	 * On JWT Created Event.
	 *
	 * @param JWTCreatedEvent $event JWT Created Event
	 *
	 * @return void
	 */
	public function onJWTCreated( JWTCreatedEvent $event ) {
		$user = $event->getUser();

		$payload = $event->getData();
		$payload['username'] = $user->getUsername();

		$event->setData( $payload );
	}
}
