<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="country")
 */
class Country {

	/**
	 * @var string
	 *
	 * @ORM\Column(name="country_id", type="string", length=2)
	 * @ORM\Id
	 * @Assert\Country()
	 */
	private $id;

	/**
	 * Country
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->id = $params->getString( 'id' );
	}

	/**
	 * Set Id
	 *
	 * @param string $id Id
	 *
	 * @return self
	 */
	public function setId( string $id ) : self {
		$this->id = $id;

		return $this;
	}

	/**
	 * Id
	 *
	 * @return string
	 */
	public function getId() :? string {
		return $this->id;
	}
}
