<?php

namespace App\Entity\Takedown\Dmca;

use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;

class CommonsPost {

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * Commons Post.
	 *
	 * @param array $data Data
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->text = $params->getString( 'text' );
	}

	/**
	 * Text
	 *
	 * @return string
	 */
	public function getText() :? string {
		return $this->text;
	}

	/**
	 * Set Text
	 *
	 * @Groups({"api"})
	 *
	 * @param string $text Text
	 *
	 * @return string
	 */
	public function setText( string $text ) : self {
		$this->text = $text;

		return $this;
	}
}
