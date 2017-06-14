<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface, JWTUserInterface {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="user_id", type="integer")
	 * @ORM\Id
	 */
	private $id;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var array
	 */
	private $roles;

	/**
	 * User
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$id = $data['id'] ?? null;
		$this->id = is_int( $id ) ? $id : null;

		$username = $data['username'] ?? null;
		$this->username = is_string( $username ) ? $username : null;

		$roles = $data['roles'] ?? null;
		$this->roles = is_array( $roles ) ? $roles : [];
	}

	/**
	 * Set Id.
	 *
	 * @param int $id ID
	 *
	 * @return self
	 */
	public function setId( int $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Get Id
	 *
	 * @return int
	 */
	public function getId() :? int {
		return $this->id;
	}

	/**
	 * Set Roles.
	 *
	 * @param array $roles Roles
	 *
	 * @return self
	 */
	public function setRoles( array $roles ) {
		$this->roles = $roles;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return null
	 */
	public function getPassword() {
		return null;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return null
	 */
	public function getSalt() {
		return null;
	}

	/**
	 * Set Username.
	 *
	 * @param string $username Username
	 *
	 * @return self
	 */
	public function setUsername( string $username ) {
		$this->username = $username;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return null
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return void
	 */
	public function eraseCredentials() {
		return;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $username Username
	 * @param array $payload Payload
	 *
	 * @return JWTUserInterface
	 */
	public static function createFromPayload( $username, array $payload ) {
		return new static( $payload );
	}

}
