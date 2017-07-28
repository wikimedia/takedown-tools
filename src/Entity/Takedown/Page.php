<?php

namespace App\Entity\Takedown;

use App\Entity\Site;
use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityAttacher\Annotation\Attach;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown_page")
 */
class Page {

	/**
	 * @var Takedown
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(
	 *	targetEntity="App\Entity\Takedown\Takedown",
	 *	inversedBy="pages"
	 *)
	 * @ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")
	 * @Attach()
	 */
	private $takedown;

	/**
	 * @var string
	 *
	 * The Prefixed DB Key.
	 *
	 * @ORM\Id
	 * @ORM\Column(name="`key`", type="string", length=255)
	 */
	private $key;

	/**
	 * Takedown
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->takedown = $params->getInstance( 'takedown', Takedown::class, new Takedown() );
		$this->key = $params->getString( 'key' );
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

	/**
	 * Set Key.
	 *
	 * @param string $key Prefixed DB Key.
	 *
	 * @return self
	 */
	public function setKey( string $key ) {
		$this->key = $key;

		return $this;
	}

	/**
	 * Get Key
	 *
	 * @Assert\Length(max=255)
	 *
	 * @return string
	 */
	public function getKey() :? string {
		return $this->key;
	}

	/**
	 * Get URL
	 *
	 * @param Site|null $site Site
	 *
	 * @return string
	 */
	public function getUrl( ?Site $site = null ) : string {
		$path = '/wiki/' . $this->key;

		if ( !$site ) {
			return $path;
		}

		if ( $site->getInfo() ) {
			$info = $site->getInfo();

			if ( !empty( $info['general']['articlepath'] ) ) {
				$template = $info['general']['articlepath'];
				$path = preg_replace( '/^(.*)$/', $template, $this->key );
			}
		}

		return 'https://' . $site->getDomain() . $path;
	}
}
