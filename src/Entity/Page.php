<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityAttacher\Annotation\Attach;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="page")
 *
 * @todo add validation.
 */
class Page {

	/**
	 * @var Site
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="App\Entity\Site")
	 * @ORM\JoinColumn(name="site", referencedColumnName="site_id")
	 * @Attach()
	 */
	private $site;

	/**
	 * @var int
	 *
	 * This should be the Prefixed DB Key.
	 *
	 * @ORM\Id
	 * @ORM\Column(name="`key`", type="string", length=255)
	 */
	private $key;

	/**
	 * Site
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->site = $params->getInstance( 'site', Site::class );
		$this->key = $params->getString( 'key' );
	}

	/**
	 * Set Site.
	 *
	 * @param Site $site Site
	 *
	 * @return self
	 */
	public function setSite( Site $site ) : self {
		$this->site = $site;

		return $this;
	}

	/**
	 * Site
	 *
	 * @return Site
	 */
	public function getSite() :? Site {
		return $this->site;
	}

	/**
	 * Get Site Id
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getSiteId() :? string {
		if ( $this->site ) {
			return $this->site->getId();
		}

		return null;
	}

	/**
	 * Get Reporter
	 *
	 * @Groups({"api"})
	 *
	 * @param int $siteId Site Id.
	 *
	 * @return User
	 */
	public function setSiteId( string $siteId ) : self {
		$this->site = new Site( [
			'id' => $siteId,
		] );

		return $this;
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
}
