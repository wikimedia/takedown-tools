<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityUtils\ParameterBag;

/**
 * @ORM\Entity
 * @ORM\Table(name="action")
 */
class Action {

	/**
	 * @var string
	 *
	 * @ORM\Column(name="action_id", type="string", length=7)
	 * @ORM\Id
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
