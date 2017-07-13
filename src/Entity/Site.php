<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityUtils\ParameterBag;

/**
 * @ORM\Entity
 * @ORM\Table(name="site")
 *
 * @todo add validation.
 */
class Site {

	/**
	 * @var string
	 *
	 * @ORM\Column(name="site_id", type="string", length=31)
	 * @ORM\Id
	 */
	private $id;

	/**
	 * @var string
	 */
	private $domain;

	/**
	 * Site
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->id = $params->getString( 'id' );
		$this->domain = $params->getString( 'domain' );
	}

	/**
	 * Set Id.
	 *
	 * @param string $id ID
	 *
	 * @return self
	 */
	public function setId( string $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Get Id
	 *
	 * @return string
	 */
	public function getId() :? string {
		return $this->id;
	}

	/**
	 * Set Domain.
	 *
	 * @param string $domain Domain
	 *
	 * @return self
	 */
	public function setDomain( string $domain ) {
		$this->domain = $domain;

		return $this;
	}

	/**
	 * Get Domain
	 *
	 * @return string
	 */
	public function getDomain() :? string {
		return $this->domain;
	}
}
