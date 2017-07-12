<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JWTCreatedEventListener {

	/**
	 * @var NormalizerInterface
	 */
	protected $normalizer;

	/**
	 * JWTCreatedEventListener
	 *
	 * @param NormalizerInterface $normalizer Normalizer
	 */
	public function __construct( NormalizerInterface $normalizer ) {
		$this->normalizer = $normalizer;
	}

	/**
	 * On JWT Created Event.
	 *
	 * @param JWTCreatedEvent $event JWT Created Event
	 *
	 * @return void
	 */
	public function onJWTCreated( JWTCreatedEvent $event ) {
		$user = $event->getUser();

		$data = $this->normalizer->normalize( $user, null, [
			'groups' => [ 'token' ]
		] );
		$payload = array_merge( $event->getData(), $data );

		$event->setData( $payload );
	}
}
