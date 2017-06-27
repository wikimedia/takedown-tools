<?php

namespace App\Client;

use App\Entity\User;
use App\Entity\Site;

interface MediaWikiClientInterface {

	/**
	 * Get User by Username
	 *
	 * @param string[] $usernames Users to retrieve.
	 *
	 * @return User[]
	 */
	public function getUsersByUsernames( array $usernames ) : array;

	/**
	 * Get User by Username
	 *
	 * @param string $username Users to retrieve.
	 *
	 * @return User
	 */
	public function getUserByUsername( string $username ) : User;

	/**
	 * Get Site by Id
	 *
	 * @param string $id Site to retrieve
	 *
	 * @return Site
	 */
	public function getSiteById( string $id ) : Site;

}
