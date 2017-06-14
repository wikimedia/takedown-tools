<?php

namespace App\Entity;

use App\EntityUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="project")
 *
 * @TODO add validation.
 */
class Project {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="project_id", type="string", length=31)
	 * @ORM\Id
	 */
	private $id;

	/**
	 * Project
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$id = $data['id'] ?? null;
		$this->id = is_string( $id ) ? $id : null;
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

}
