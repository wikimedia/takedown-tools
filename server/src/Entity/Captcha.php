<?php

namespace App\Entity;

use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Captcha
 */
class Captcha {

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $word;

	/**
	 * Captcha
	 *
	 * @param array $data Data
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->id = $params->getString( 'id' );
		$this->url = $params->getString( 'url' );
		$this->word = $params->getString( 'word' );
	}

	/**
	 * Id
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getId() :? string {
		return $this->id;
	}

	/**
	 * Set Id
	 *
	 * @Groups({"api"})
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
	 * Url
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getUrl() :? string {
		return $this->url;
	}

	/**
	 * Set Url
	 *
	 * @Groups({"api"})
	 *
	 * @param string $url Url
	 *
	 * @return self
	 */
	public function setUrl( string $url ) : self {
		$this->url = $url;

		return $this;
	}

	/**
	 * Wrod
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getWord() :? string {
		return $this->word;
	}

	/**
	 * Set Word
	 *
	 * @Groups({"api"})
	 *
	 * @param string $word Word
	 *
	 * @return self
	 */
	public function setWord( string $word ) : self {
		$this->word = $word;

		return $this;
	}

}
