<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @Route(service="app.controller_user")
 */
class UserController {

	/**
	 * @var RegistryInterface
	 */
	protected $doctrine;

	/**
	 * Takedown Controller.
	 *
	 * @param RegistryInterface $doctrine Doctrine.
	 */
	public function __construct(
		RegistryInterface $doctrine
	) {
		$this->doctrine = $doctrine;
	}

	/**
	 * User
	 *
	 * @Route("/api/user/{user}.{_format}", defaults={"_format" = "json"})
	 * @Method({"GET"})
	 *
	 * @param User $user User
	 *
	 * @return User
	 */
	public function showAction( User $user ) : User {
		return $user;
	}

}
