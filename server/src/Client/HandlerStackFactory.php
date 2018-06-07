<?php

namespace App\Client;

use App\Entity\User;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Handler Stack Factory
 */
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
		$user = $this->getUser();

		if ( $user ) {
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

	/**
	 * Get a user from the Security Token Storage.
	 *
	 * @return User
	 */
	 protected function getUser() :? User {
		 $token = $this->tokenStorage->getToken();

		 if ( $token === null ) {
			 return $token;
		 }

		 $user = $token->getUser();

		 if ( ! $user instanceof User ) {
				 return null;
		 }

		 return $user;
	 }
}
