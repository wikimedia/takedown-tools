<?php

namespace App\Entity\Takedown\Dmca;

use App\Entity\Action;
use App\Entity\Country;
use App\Entity\Takedown\Takedown;
use App\Entity\Takedown\Dmca\Page;
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
	 * @ORM\Column(name="ce_send", type="boolean", options={"default"=false})
	 */
	private $ceSend;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ce_title", type="string", length=255, nullable=true)
	 */
	 private $ceTitle;

	/**
	 * @var Collection
	 *
	 * @ORM\OneToMany(
	 * 	targetEntity="App\Entity\Takedown\Dmca\Page",
	 * 	mappedBy="dmca",
	 * 	cascade={"persist"}
	 *)
	 */
	private $pages;

	/**
	 * @var Collection
	 *
	 * @ORM\OneToMany(
	 * 	targetEntity="App\Entity\Takedown\Dmca\Original",
	 * 	mappedBy="dmca",
	 * 	cascade={"persist"}
	 *)
	 */
	private $originals;

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
	 * @var \DateTimeInterface
	 *
	 * @ORM\Column(name="sent", type="date", nullable=true)
	 */
	private $sent;

	/**
	* @var Action
	*
	* @ORM\ManyToOne(targetEntity="App\Entity\Action")
	* @ORM\JoinColumn(name="action_taken", referencedColumnName="action_id")
	* @Attach()
	*/
	private $actionTaken;

	/**
	 * Takedown
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->takedown = $params->getInstance( 'takedown', Takedown::class, new Takedown() );
		$this->ceSend = $params->getBoolean( 'ceSend', false );
		$this->ceTitle = $params->getString( 'ceTitle' );
		$this->pages = $params->getCollection(
			'pages',
			Page::class,
			new ArrayCollection()
		);
		$this->originals = $params->getCollection(
			'originals',
			Original::class,
			new ArrayCollection()
		);
		$this->senderName = $params->getString( 'name' );
		$this->senderPerson = $params->getString( 'person' );
		$this->senderFirm = $params->getString( 'firm' );
		$this->senderAddress1 = $params->getString( 'address1' );
		$this->senderAddress2 = $params->getString( 'address2' );
		$this->senderCity = $params->getString( 'city' );
		$this->senderCountry = $params->getInstance( 'country', Country::class );
		$this->date = $params->getInstance( 'sent', \DateTime::class );
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
	 * Set Send to Chilling Effects
	 *
	 * @Groups({"api"})
	 *
	 * @param bool $ceSend Send CE?
	 *
	 * @return self
	 */
	public function setCeSend( bool $ceSend ) : self {
		$this->ceSend = $ceSend;

		return $this;
	}

	/**
	 * Send to Chilling Effects
	 *
	 * @Groups({"api"})
	 *
	 * @return bool
	 */
	public function getCeSend() :? bool {
		return $this->ceSend;
	}

	/**
	 * Set Chilling Effects Title
	 *
	 * @Groups({"api"})
	 *
	 * @param string $ceTitle Chilling Effects Title
	 *
	 * @return self
	 */
	public function setCeTitle( string $ceTitle ) : self {
		$this->ceTitle = $ceTitle;

		return $this;
	}

	/**
	 * Chilling Effects Title
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getCeTitle() :? string {
		return $this->ceTitle;
	}

	/**
	 * Pages
	 *
	 * @return Collection
	 */
	public function getPages() : Collection {
		return $this->pages;
	}

	/**
	 * Add Page
	 *
	 * @param Page $page Page
	 *
	 * @return self
	 */
	public function addPage( Page $page ) : self {
		$this->pages->add( $page );

		return $this;
	}

	/**
	 * Remove Page
	 *
	 * @param Page $page Page
	 *
	 * @return self
	 */
	public function removePage( Page $page ) : self {
		$this->pages->remove( $page );

		return $this;
	}

	/**
	 * Pages
	 *
	 * @Groups({"api"})
	 *
	 * @return array
	 */
	public function getPageIds() : array {
		return $this->pages->map( function ( $page ) {
			return $page->getKey();
		} )->toArray();
	}

	/**
	 * Pages
	 *
	 * @Groups({"api"})
	 *
	 * @param string[] $pageIds Page ids
	 *
	 * @return Collection
	 */
	public function setPageIds( array $pageIds ) : self {
		$this->pages = new ArrayCollection( array_map( function ( $id ) {
			return new Page( [
				'key' => $id,
				'dmca' => $this,
			] );
		}, $pageIds ) );

		return $this;
	}

	/**
	 * Originals
	 *
	 * @return Collection
	 */
	public function getOriginals() : Collection {
		return $this->originals;
	}

	/**
	 * Add Original
	 *
	 * @param Original $original Original
	 *
	 * @return self
	 */
	public function addOriginal( Original $original ) : self {
		$this->originals->add( $original );

		return $this;
	}

	/**
	 * Remove Original
	 *
	 * @param Original $original Original
	 *
	 * @return self
	 */
	public function removeOriginal( Original $original ) : self {
		$this->originals->remove( $original );

		return $this;
	}

	/**
	 * Originals
	 *
	 * @Groups({"api"})
	 *
	 * @return array
	 */
	public function getOriginalUrls() : array {
		return $this->originals->map( function ( $original ) {
			return $original->getUrl();
		} )->toArray();
	}

	/**
	 * Originals
	 *
	 * @Groups({"api"})
	 *
	 * @param string[] $originalUrls Original urls.
	 *
	 * @return Collection
	 */
	public function setOriginalUrls( array $originalUrls ) : self {
		$this->originals = new ArrayCollection( array_map( function ( $url ) {
			return new Original( [
				'url' => $url,
				'dmca' => $this,
			] );
		}, $originalUrls ) );

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

	/**
	 * Set sent
	 *
	 * @Groups({"api"})
	 *
	 * @param \DateTimeInterface $sent The Sent Date.
	 *
	 * @return self
	 */
	public function setSent( \DateTimeInterface $sent ) : self {
			$this->sent = $sent;

			return $this;
	}

	/**
	 * Sent
	 *
	 * @Groups({"api"})
	 *
	 * @return \DateTime
	 */
	public function getSent() :? \DateTimeInterface {
			return $this->sent;
	}

	/**
	 * Set Action Taken
	 *
	 * @param Action $actionTaken Action Taken.
	 *
	 * @return self
	 */
	public function setActionTaken( Action $actionTaken ) : self {
			$this->actionTaken = $actionTaken;

			return $this;
	}

	/**
	 * Action Taken
	 *
	 *
	 * @return \DateTime
	 */
	public function getActionTaken() :? Action {
			return $this->actionTaken;
	}

	/**
	 * Set Action Taken Id.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $id Action Taken Id.
	 *
	 * @return self
	 */
	public function setActionTakenId( string $id ) : self {
		$this->actionTaken = new Action( [
			'id' => $id,
		] );

		return $this;
	}

	/**
	 * Action Taken Id.
	 *
	 * @Groups({"api"})
	 *
	 * @return Country
	 */
	public function getActionTakenId() :? string {
		if ( !$this->actionTaken ) {
			return null;
		}

		return $this->actionTaken->getId();
	}

	/**
	 * Clone
	 *
	 * @return void
	 */
	public function __clone() {
		$this->pages = $this->pages->map( function( $page ) {
			return new Page( [
					'key' => $page->getKey(),
					'dmca' => $this,
			] );
		} );

		$this->originals = $this->originals->map( function( $original ) {
			return new Original( [
					'url' => $original->getUrl(),
					'dmca' => $this,
			] );
		} );
	}
}
