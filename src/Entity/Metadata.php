<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata")
 */
class Metadata {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="metadata_id", type="string", length=31)
	 * @ORM\Id
	 */
	private $id;

	/**
	 * Metadata
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$id = $data['id'] ?? null;
		$this->id = is_string( $id ) ? $id : null;
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
