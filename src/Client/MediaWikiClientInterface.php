<?php

namespace App\Client;

use App\Entity\Site;
use App\Entity\User;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * MediaWiki Client Interface
 */
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

	/**
	 * Get Site by Id
	 *
	 * @param string $id Site to retrieve
	 *
	 * @return Site
	 */
	public function getSite( string $id ) : PromiseInterface;
}
