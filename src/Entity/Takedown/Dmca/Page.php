<?php

namespace App\Entity\Takedown\Dmca;

use App\Entity\Takedown\Dmca\Dmca;
use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityAttacher\Annotation\Attach;
use GeoSocio\EntityUtils\ParameterBag;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown_dmca_page")
 *
 * @todo add validation.
 */
class Page {

	/**
	 * @var Dmca
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(
	 *	targetEntity="App\Entity\Takedown\Dmca\Dmca",
	 *	inversedBy="pages"
	 *)
	 * @ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")
	 * @Attach()
	 */
	private $dmca;

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
		$this->dmca = $params->getInstance( 'dmca', Dmca::class, new Dmca() );
		$this->key = $params->getString( 'key' );
	}

	/**
	 * Set Dmca
	 *
	 * @param Dmca $dmca Dmca
	 *
	 * @return self
	 */
	public function setDmca( Dmca $dmca ) : self {
		$this->dmca = $dmca;

		return $this;
	}

	/**
	 * Get Dmca
	 *
	 * @return Dmca
	 */
	public function getDmca() :? Dmca {
		return $this->dmca;
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
	 * @return string
	 */
	public function getKey() :? string {
		return $this->key;
	}

	/**
	 * Clone
	 *
	 * @return Page
	 */
	public function __clone() {
		$this->pages = $this->pages->map( function( $page ) {
			if ( $this->takedown && $this->takedown->getSite() ) {
				$page->setSite( $this->takedown->getSite() );
			}

			return $page;
		} );
	}
}
