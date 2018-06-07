<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Serializer\Annotation\Groups;

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
	 * @ParamConverter("user", class="App\Entity\User")
	 * @Method({"GET"})
	 * @Groups({"api"})
	 *
	 * @param User $user User
	 *
	 * @return User
	 */
	public function showAction( User $user ) : User {
		return $user;
	}

}
