<?php

namespace App\Entity;

use GeoSocio\EntityUtils\ParameterBag;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
	 *
	 * @ORM\Column(name="username", type="string", length=255, unique=true, nullable=true)
	 */
	private $username;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var bool
	 */
	private $emailVerified;

	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var string
	 */
	private $secret;

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
		$params = new ParameterBag( $data );
		$this->id = $params->getInt( 'id' );
		$this->username = $params->getString( 'username' );
		$this->email = $params->getString( 'email' );
		$this->emailVerified = $params->getBoolean( 'emailVerified' );
		$this->token = $params->getString( 'token' );
		$this->secret = $params->getString( 'secret' );
		$this->roles = $params->getArray( 'roles', [] );
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
	 * @Groups({"api"})
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
	 * @Groups({"api"})
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
	 * Email
	 *
	 * @Groups({"token"})
	 * @Assert\Email()
	 *
	 * @return null
	 */
	public function getEmail() :? string {
		return $this->email;
	}

	/**
	 * Set Email.
	 *
	 * @param string $email Email
	 *
	 * @return self
	 */
	public function setEmail( string $email ) : self {
		$this->email = $email;

		return $this;
	}

	/**
	 * Email Verified
	 *
	 * @Groups({"token"})
	 *
	 * @return null
	 */
	public function isEmailVerified() :? bool {
		return $this->emailVerified;
	}

	/**
	 * Set Email Verified
	 *
	 * @param bool $emailVerified Email Verified
	 *
	 * @return self
	 */
	public function setEmailVerified( bool $emailVerified ) : self {
		$this->emailVerified = $emailVerified;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @Groups({"api", "token"})
	 *
	 * @return null
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Set Token.
	 *
	 * @param string $token Token
	 *
	 * @return self
	 */
	public function setToken( string $token ) : self {
		$this->token = $token;

		return $this;
	}

	/**
	 * Token
	 *
	 * @Groups({"token"})
	 *
	 * @return null
	 */
	public function getToken() :? string {
		return $this->token;
	}

	/**
	 * Set Secret.
	 *
	 * @param string $secret Secret
	 *
	 * @return self
	 */
	public function setSecret( string $secret ) : self {
		$this->secret = $secret;

		return $this;
	}

	/**
	 * Secret
	 *
	 * @Groups({"token"})
	 *
	 * @return string
	 */
	public function getSecret() :? string {
		return $this->secret;
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
	 * @param mixed $username Username
	 * @param array $payload Payload
	 *
	 * @return JWTUserInterface
	 */
	public static function createFromPayload( $username, array $payload ) {
		return new static( $payload );
	}

	/**
	* Get roles from groups.
	*
	* @param array $groups Groups.
	*
	* @return array
	*/
	public static function getRolesFromGroups( array $groups ) : array {
		$groups = array_filter( $groups, function ( $group ) {
			return $group !== "*";
		} );

		$roles = array_map( function ( $group ) {
			return 'ROLE_' . strtoupper( $group );
		}, $groups );

		return array_values( $roles );
	}

	/**
	 * Get URL
	 *
	 * @param Site|null $site Site
	 *
	 * @return string
	 */
	public function getUrl( ?Site $site = null ) : string {
		$path = '/wiki/User:' . $this->username;

		if ( !$site ) {
			return $path;
		}

		if ( $site->getInfo() ) {
			$info = $site->getInfo();

			if ( !empty( $info['general']['articlepath'] ) ) {
				$template = $info['general']['articlepath'];
				$path = preg_replace( '/^(.*)$/', $template, 'User:' . $this->username );
			}
		}

		return 'https://' . $site->getDomain() . $path;
	}

}
