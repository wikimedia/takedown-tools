<?php

namespace App\Client;

use App\Entity\User;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HandlerStackFactory {

	/**
	 * @var TokenStorageInterface
	 */
	protected $tokenStorage;

	/**
	 * @var string
	 */
	protected $comsumerKey;

	/**
	 * @var string
	 */
	protected $consumerSecret;

	/**
	 * HandlerStackFactory
	 *
	 * @param TokenStorageInterface $tokenStorage Token Storage
	 * @param string $consumerKey Consumer Key
	 * @param string $consumerSecret Consumer Secret
	 */
	public function __construct(
			TokenStorageInterface $tokenStorage,
			string $consumerKey,
			string $consumerSecret
		) {
		$this->tokenStorage = $tokenStorage;
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
	}

	/**
	 * Create Handler Stack.
	 *
	 * @return HandlerStack
	 */
	public function createHandlerStack() {
		$stack = HandlerStack::create();
		$user = $this->tokenStorage->getToken()->getUser();

		if ( $user instanceof User ) {
				$oauth = new Oauth1( [
					'consumer_key' => $this->consumerKey,
					'consumer_secret' => $this->consumerSecret,
					'token' => $user->getToken(),
					'token_secret' => $user->getSecret()
				] );
				$stack->push( $oauth );
		}

		return $stack;
	}
}
