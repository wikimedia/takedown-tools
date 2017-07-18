<?php

namespace App\Entity\Takedown\Dmca;

use App\Entity\Captcha;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Commons Post
 */
class Post {

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * @var Captcha
	 */
	protected $captcha;

	/**
	 * Commons Post.
	 *
	 * @param array $data Data
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->text = $params->getString( 'text' );
		$this->captcha = $params->getInstance( 'captcha', Captcha::class );
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

	/**
	 * Captcha
	 *
	 * @Groups({"api"})
	 *
	 * @return Captcha
	 */
	public function getCaptcha() :? Captcha {
		return $this->captcha;
	}

	/**
	 * Set Captcha
	 *
	 * @Groups({"api"})
	 *
	 * @param Captcha $captcha Captcha
	 *
	 * @return self
	 */
	public function setCaptcha( Captcha $captcha ) : self {
		$this->captcha = $captcha;

		return $this;
	}
}
