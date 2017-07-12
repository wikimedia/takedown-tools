<?php

namespace App\Client;

use App\Entity\User;
use GuzzleHttp\Promise\PromiseInterface;

interface MediaWikiClientInterface {

	/**
	 * Get User by Username
	 *
	 * @param string[] $usernames Users to retrieve.
	 *
	 * @return User[]
	 */
	public function getUsers( array $usernames ) : PromiseInterface;

	/**
	 * Get User by Username
	 *
	 * @param string $username Users to retrieve.
	 *
	 * @return User
	 */
	public function getUser( string $username ) : PromiseInterface;
}
