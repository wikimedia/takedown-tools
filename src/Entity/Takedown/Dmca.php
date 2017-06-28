<?php

namespace App\Entity\Takedown;

use App\Entity\Country;
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
	 * @var string
	 *
	 * @ORM\Column(name="sender_name", type="string", length=63, nullable=true)
	 */
	 private $senderName;

	 /**
	  * @var string
	  *
	  * @ORM\Column(name="sender_person", type="string", length=63, nullable=true)
	  */
	 private $senderPerson;

	 /**
	  * @var string
	  *
	  * @ORM\Column(name="sender_firm", type="string", length=63, nullable=true)
	  */
	 private $senderFirm;

	 /**
	  * @var string
	  *
	  * @ORM\Column(name="sender_address_1", type="string", length=127, nullable=true)
	  */
	 private $senderAddress1;

	 /**
	  * @var string
	  *
	  * @ORM\Column(name="sender_address_2", type="string", length=127, nullable=true)
	  */
	 private $senderAddress2;

	 /**
	  * @var string
	  *
	  * @ORM\Column(name="sender_city", type="string", length=63, nullable=true)
	  */
	 private $senderCity;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="sender_state", type="string", length=63, nullable=true)
	 */
	private $senderState;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="sender_zip", type="string", length=15, nullable=true)
	 */
	private $senderZip;

	 /**
	  * @var Country
	  *
	  * @ORM\ManyToOne(targetEntity="App\Entity\Country")
	  * @ORM\JoinColumn(name="sender_country", referencedColumnName="country_id")
	  * @Attach()
	  */
		private $senderCountry;

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
		$this->senderName = $params->getString( 'name' );
		$this->senderPerson = $params->getString( 'person' );
		$this->senderFirm = $params->getString( 'firm' );
		$this->senderAddress1 = $params->getString( 'address1' );
		$this->senderAddress2 = $params->getString( 'address2' );
		$this->senderCity = $params->getString( 'city' );
		$this->senderCountry = $params->getInstance( 'country', Country::class );
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

	/**
	 * Set Sender Name.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $senderName Name.
	 *
	 * @return self
	 */
	public function setSenderName( string $senderName ) : self {
		$this->senderName = $senderName;

		return $this;
	}

	/**
	 * Name.
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getSenderName() :? string {
		return $this->senderName;
	}

	/**
	 * Set Person.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $senderPerson Person.
	 *
	 * @return self
	 */
	public function setSenderPerson( string $senderPerson ) : self {
		$this->senderPerson = $senderPerson;

		return $this;
	}

	/**
	 * Person.
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getSenderPerson() :? string {
		return $this->senderPerson;
	}

	/**
	 * Set Firm.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $senderFirm Firm.
	 *
	 * @return self
	 */
	public function setSenderFirm( string $senderFirm ) : self {
		$this->senderFirm = $senderFirm;

		return $this;
	}

	/**
	 * Firm.
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getSenderFirm() :? string {
		return $this->senderFirm;
	}

	/**
	 * Set Address.
	 *
	 * @Groups({"api"})
	 *
	 * @param array $senderAddress Address, may not exceed two lines.
	 *
	 * @return self
	 */
	public function setSenderAddress( array $senderAddress ) : self {
		$senderAddress = array_values( $senderAddress );

		if ( count( $senderAddress ) > 2 ) {
			throw new \InvalidArgumentException( 'Address may not exceed 2 lines' );
		}

		$this->senderAddress1 = isset( $senderAddress[0] ) ? $senderAddress[0] : null;
		$this->senderAddress2 = isset( $senderAddress[1] ) ? $senderAddress[1] : null;

		return $this;
	}

	/**
	 * Address.
	 *
	 * @Groups({"api"})
	 *
	 * @return string[]
	 */
	public function getSenderAddress() :? array {
		$address = [
			$this->senderAddress1,
			$this->senderAddress2,
		];

		return array_filter( $address, function ( $line ) {
			return !empty( $line );
		} );
	}

	/**
	 * Set City.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $senderCity City.
	 *
	 * @return self
	 */
	public function setSenderCity( string $senderCity ) : self {
		$this->senderCity = $senderCity;

		return $this;
	}

	/**
	 * City.
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getSenderCity() :? string {
		return $this->senderCity;
	}

	/**
	 * Set State.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $senderState State/Providence.
	 *
	 * @return self
	 */
	public function setSenderState( string $senderState ) : self {
		$this->senderState = $senderState;

		return $this;
	}

	/**
	 * State.
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getSenderState() :? string {
		return $this->senderState;
	}

	/**
	 * Set Zip.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $senderZip Zip/Postal Code.
	 *
	 * @return self
	 */
	public function setSenderZip( string $senderZip ) : self {
		$this->senderZip = $senderZip;

		return $this;
	}

	/**
	 * Zip.
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getSenderZip() :? string {
		return $this->senderZip;
	}

	/**
	 * Set Sender Country.
	 *
	 * @param Country $senderCountry Country.
	 *
	 * @return self
	 */
	public function setSenderCountry( Country $senderCountry ) : self {
		$this->senderCountry = $senderCountry;

		return $this;
	}

	/**
	 * Country.
	 *
	 * @return Country
	 */
	public function getSenderCountry() :? Country {
		return $this->senderCountry;
	}

	/**
	 * Set Country Code.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $code Country Code.
	 *
	 * @return self
	 */
	public function setSenderCountryCode( string $code ) : self {
		$this->senderCountry = new Country( [
			'id' => $code,
		] );

		return $this;
	}

	/**
	 * Country.
	 *
	 * @Groups({"api"})
	 *
	 * @return Country
	 */
	public function getSenderCountryCode() :? string {
		if ( !$this->senderCountry ) {
			return null;
		}

		return $this->senderCountry->getId();
	}
}
