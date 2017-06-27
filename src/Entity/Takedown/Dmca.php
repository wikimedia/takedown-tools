<?php

namespace App\Entity\Takedown;

use App\Entity\ContentType;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityAttacher\Annotation\Attach;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown_dmca")
 *
 * @todo add validation.
 */
class Dmca {

	/**
	 * @var Takedown
	 *
	 * @ORM\Id
	 * @ORM\OneToOne(
	 *	targetEntity="App\Entity\Takedown\Takedown",
	 *	inversedBy="dmca"
	 *)
   * @ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")
	 */
	private $takedown;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="send_ce", type="boolean", options={"default"=false})
	 */
	private $sendCe;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToMany(targetEntity="App\Entity\ContentType")
	 * @ORM\JoinTable(name="takedown_dmca_content_type",
	 *      joinColumns={@ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(
	 *        name="content_type_id",
	 *        referencedColumnName="content_type_id"
	 *      )}
	 * )
	 * @Attach()
	 */
	private $contentTypes;

	/**
	 * Takedown
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->takedown = $params->getInstance( 'takedown', Takedown::class, new Takedown() );
		$this->sendCe = $params->getBoolean( 'sendCe', false );
		$this->contentTypes = $params->getCollection(
			'contentTypes',
			ContentType::class,
			new ArrayCollection()
		);
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
	 * Set Send CE
	 *
	 * @Groups({"api"})
	 *
	 * @param bool $sendCe Send CE?
	 *
	 * @return self
	 */
	public function setSendCe( bool $sendCe ) : self {
		$this->sendCe = $sendCe;

		return $this;
	}

	/**
	 * Send CE
	 *
	 * @Groups({"api"})
	 *
	 * @return bool
	 */
	public function getSendCe() :? bool {
		return $this->sendCe;
	}

	/**
	 * Metadata
	 *
	 * @return Collection
	 */
	public function getContentTypes() : Collection {
		return $this->contentTypes;
	}

	/**
	 * Add Content Type
	 *
	 * @param ContentType $contentType Content Type
	 *
	 * @return self
	 */
	public function addContentType( ContentType $contentType ) : self {
		$this->contentTypes->add( $contentType );

		return $this;
	}

	/**
	 * Remove Content Type
	 *
	 * @param ContentType $contentType Content Type
	 *
	 * @return self
	 */
	public function removeContentType( ContentType $contentType ) : self {
		$this->contentTypes->remove( $metadata );

		return $this;
	}

	/**
	 * Content Type Ids
	 *
	 * @Groups({"api"})
	 *
	 * @return Collection
	 */
	public function getContentTypeIds() : array {
		return $this->contentTypes->map( function ( $contentType ) {
			return $contentType->getId();
		} )->toArray();
	}

	/**
	 * Set Content Type Ids
	 *
	 * @Groups({"api"})
	 *
	 * @param array $contentTypeIds Content Type Ids
	 *
	 * @return self
	 */
	public function setContentTypeIds( array $contentTypeIds ) : self {
		$contentTypes = array_map( function ( $id ) {
			return new ContentType( [
				'id' => $id,
			] );
		}, $contentTypeIds );

		$this->contentTypes = new ArrayCollection( $contentTypes );

		return $this;
	}
}
