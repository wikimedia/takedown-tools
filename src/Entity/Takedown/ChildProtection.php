<?php

namespace App\Entity\Takedown;

use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityUtils\ParameterBag;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown_cp")
 *
 * @@todo add validation.
 */
class ChildProtection {

	/**
	 * @var Takedown
	 *
	 * @ORM\Id
	 * @ORM\OneToOne(targetEntity="App\Entity\Takedown\Takedown", inversedBy="cp")
   * @ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")
	 */
	private $takedown;

	/**
	 * Takedown
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->takedown = $params->getInstance( 'takedown', Takedown::class, new Takedown() );
	}

	/**
	 * Set Takedown
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return self
	 */
	public function setTakedown( Takedown $takedown ) : self {
		$this->takedown = $takedown;

		return $this;
	}

	/**
	 * Get Takedown
	 *
	 * @return Takedown
	 */
	public function getTakedown() :? Takedown {
		return $this->takedown;
	}
}
