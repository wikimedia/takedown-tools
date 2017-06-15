<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata")
 */
class Metadata {

	/**
	 * @var string
	 *
	 * @ORM\Column(name="metadata_id", type="string", length=31)
	 * @ORM\Id
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="content", type="string", length=255)
	 */
	private $content;

	/**
	 * Metadata
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$id = $data['id'] ?? null;
		$this->id = is_string( $id ) ? $id : null;

		$content = $data['content'] ?? null;
		$this->content = is_string( $content ) ? $content : null;
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

	/**
	 * Set Content
	 *
	 * @param string $content Content
	 *
	 * @return self
	 */
	public function setContent( string $content ) : self {
		$this->content = $content;

		return $this;
	}

	/**
	 * Content
	 *
	 * @return string
	 */
	public function getContent() :? string {
		return $this->content;
	}
}
