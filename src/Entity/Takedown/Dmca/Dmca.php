<?php

namespace App\Entity\Takedown\Dmca;

use App\Entity\File;
use App\Entity\Action;
use App\Entity\Country;
use App\Entity\Takedown\Takedown;
use App\Entity\Takedown\Dmca\Page;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityAttacher\Annotation\Attach;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown_dmca")
 *
 * @Assert\GroupSequenceProvider
 */
class Dmca implements GroupSequenceProviderInterface {

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
	 */
	private $lumenSend;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="lumen_id", type="integer", nullable=true)
	 */
	private $lumenId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="lumen_title", type="string", length=255, nullable=true)
	 */
	 private $lumenTitle;

	/**
	 * @var Collection
	 *
	 * @ORM\OneToMany(
	 * 	targetEntity="App\Entity\Takedown\Dmca\Page",
	 * 	mappedBy="dmca",
	 * 	cascade={"persist", "remove"}
	 *)
	 */
	private $pages;

	/**
	 * @var Collection
	 *
	 * @ORM\OneToMany(
	 * 	targetEntity="App\Entity\Takedown\Dmca\Original",
	 * 	mappedBy="dmca",
	 * 	cascade={"persist", "remove"}
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
	 * @var string
	 *
	 * @ORM\Column(name="method", type="string", length=127, nullable=true)
	 */
	 private $method;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="subject", type="string", length=255, nullable=true)
	 */
	 private $subject;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="body", type="text", nullable=true)
	 */
	 private $body;

	 /**
		* @var string
		*
		* @ORM\Column(name="wmf_title", type="string", length=255, nullable=true)
		*/
	 private $wmfTitle;

	 /**
		* @var bool
		*
		* @ORM\Column(name="commons_send", type="boolean", options={"default"=false})
		*/
	private $commonsSend;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="commons_vp_send", type="boolean", options={"default"=false})
	 */
 private $commonsVillagePumpSend;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToMany(targetEntity="App\Entity\File", cascade={"remove"})
	 * @ORM\JoinTable(name="takedown_dmca_file",
	 *      joinColumns={
	 *				@ORM\JoinColumn(
	 *					name="takedown_id",
	 *					referencedColumnName="takedown_id",
	 *				)
	 *			},
	 *      inverseJoinColumns={@ORM\JoinColumn(
	 *        name="file_id",
	 *        referencedColumnName="file_id",
	 *        onDelete="cascade"
	 *      )}
	 * )
	 * @Attach()
	 */
	private $files;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", cascade={"persist"})
	 * @ORM\JoinTable(name="takedown_dmca_user_notice",
	 *      joinColumns={@ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(
	 *        name="user_id",
	 *        referencedColumnName="user_id"
	 *      )}
	 * )
	 * @Attach()
	 */
	private $userNotices;

	/**
	 * Takedown
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->takedown = $params->getInstance( 'takedown', Takedown::class, new Takedown() );
		$this->lumenSend = $params->getBoolean( 'lumenSend', false );
		$this->lumenTitle = $params->getString( 'lumenTitle' );
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
		$this->method = $params->getString( 'method' );
		$this->subject = $params->getString( 'subject' );
		$this->body = $params->getString( 'body' );
		$this->wmfTitle = $params->getString( 'wmfTitle' );
		$this->commonsSend = $params->getBoolean( 'commonsSend', false );
		$this->commonsVillagePumpSend = $params->getBoolean( 'commonsSend', false );
		$this->files = $params->getCollection(
			'files',
			File::class,
			new ArrayCollection()
		);
		$this->userNotices = $params->getCollection(
			'userNotices',
			User::class,
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
	 * Set Send to Lumen
	 *
	 * @Groups({"api"})
	 *
	 * @param bool $lumenSend Send CE?
	 *
	 * @return self
	 */
	public function setLumenSend( bool $lumenSend ) : self {
		$this->lumenSend = $lumenSend;

		return $this;
	}

	/**
	 * Send to Lumen
	 *
	 * @return bool
	 */
	public function getLumenSend() :? bool {
		return $this->lumenSend;
	}

	/**
	 * Set Lumen Id
	 *
	 * @param int|null $lumenId Lumen Id
	 *
	 * @return self
	 */
	public function setLumenId( ?int $lumenId ) : self {
		$this->lumenId = $lumenId;

		return $this;
	}

	/**
	 * Lumen Id
	 *
	 * @Groups({"api"})
	 *
	 * @return bool
	 */
	public function getLumenId() :? int {
		return $this->lumenId;
	}

	/**
	 * Set Lumen Title
	 *
	 * @Groups({"api"})
	 *
	 * @param string $lumenTitle Lumen Title
	 *
	 * @return self
	 */
	public function setlumenTitle( string $lumenTitle ) : self {
		$this->lumenTitle = $lumenTitle;

		return $this;
	}

	/**
	 * Lumen Title
	 *
	 * @Groups({"api"})
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=255)
	 *
	 * @return string
	 */
	public function getlumenTitle() :? string {
		return $this->lumenTitle;
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
	 * @Assert\Count(min=1, groups={"Lumen"})
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
	 * @Assert\Count(min=1, groups={"Lumen"})
	 * @Assert\All( {
	 *     @Assert\Url()
	 * } )
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
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=63)
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
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=63)
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
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=63)
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
	 * @Assert\NotBlank(groups={"Lumen"})
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
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=63)
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
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=63)
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
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=15)
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
	 * @Assert\NotNull(groups={"Lumen"})
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
	 * @Assert\NotNull(groups={"Lumen"})
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
	 * @return Action
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
	 * @Assert\NotNull(groups={"Lumen"})
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
	 * Set Method
	 *
	 * @Groups({"api"})
	 *
	 * @param string $method Method
	 *
	 * @return self
	 */
	public function setMethod( string $method ) : self {
		$this->method = $method;

		return $this;
	}

	/**
	 * Method
	 *
	 * @Groups({"api"})
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=127)
	 *
	 * @return string
	 */
	public function getMethod() :? string {
		return $this->method;
	}

	/**
	 * Set Subject
	 *
	 * @Groups({"api"})
	 *
	 * @param string $subject Subject
	 *
	 * @return self
	 */
	public function setSubject( string $subject ) : self {
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Subject
	 *
	 * @Groups({"api"})
	 * @Assert\NotBlank(groups={"Lumen"})
	 * @Assert\Length(max=255)
	 *
	 * @return string
	 */
	public function getSubject() :? string {
		return $this->subject;
	}

	/**
	 * Set Body
	 *
	 * @Groups({"api"})
	 *
	 * @param string $body Body
	 *
	 * @return self
	 */
	public function setBody( string $body ) : self {
		$this->body = $body;

		return $this;
	}

	/**
	 * Body
	 *
	 * @Groups({"api"})
	 * @Assert\NotBlank(groups={"Lumen"})
	 *
	 * @return string
	 */
	public function getBody() :? string {
		return $this->body;
	}

	/**
	 * Set WMF Title
	 *
	 * @Groups({"api"})
	 *
	 * @param string $wmfTitle WMF Title
	 *
	 * @return self
	 */
	public function setWmfTitle( string $wmfTitle ) : self {
		$this->wmfTitle = $wmfTitle;

		return $this;
	}

	/**
	 * WMF Title
	 *
	 * @Groups({"api"})
	 * @Assert\Length(max=255)
	 *
	 * @return string
	 */
	public function getWmfTitle() :? string {
		return $this->wmfTitle;
	}

	/**
	 * Set Send to Commons
	 *
	 * @Groups({"api"})
	 *
	 * @param bool $commonsSend Send to Commons
	 *
	 * @return self
	 */
	public function setCommonsSend( bool $commonsSend ) : self {
		$this->commonsSend = $commonsSend;

		return $this;
	}

	/**
	 * Send to Commons
	 *
	 * @Groups({"api"})
	 *
	 * @return bool
	 */
	public function getCommonsSend() :? bool {
		return $this->commonsSend;
	}

	/**
	 * Set Send to Commons Village Pump
	 *
	 * @Groups({"api"})
	 *
	 * @param bool $commonsVillagePumpSend Send to Commons
	 *
	 * @return self
	 */
	public function setCommonsVillagePumpSend( bool $commonsVillagePumpSend ) : self {
		$this->commonsVillagePumpSend = $commonsVillagePumpSend;

		return $this;
	}

	/**
	 * Send to Commons
	 *
	 * @Groups({"api"})
	 *
	 * @return bool
	 */
	public function getCommonsVillagePumpSend() :? bool {
		return $this->commonsVillagePumpSend;
	}

	/**
	 * Files
	 *
	 * @return Collection
	 */
	public function getFiles() : Collection {
		return $this->files;
	}

	/**
	 * Add File
	 *
	 * @param File $file File
	 *
	 * @return self
	 */
	public function addFile( File $file ) : self {
		$this->files->add( $file );

		return $this;
	}

	/**
	 * Set Files
	 *
	 * @param Collection $files Files
	 *
	 * @return self
	 */
	public function setFiles( Collection $files ) : self {
		$this->files = $files;

		return $this;
	}

	/**
	 * Remove File
	 *
	 * @param File $file File
	 *
	 * @return self
	 */
	public function removeFile( File $file ) : self {
		$this->files->remove( $file );

		return $this;
	}

	/**
	 * Set File Ids
	 *
	 * @Groups({"api"})
	 *
	 * @param int[] $fileIds File  Ids
	 *
	 * @return Collection
	 */
	public function setFileIds( array $fileIds ) : self {
		$this->files = new ArrayCollection( array_map( function ( $id ) {
			return new File( [
				'id' => $id,
			] );
		}, $fileIds ) );

		return $this;
	}

	/**
	 * File Ids
	 *
	 * @Groups({"api"})
	 *
	 * @return Collection
	 */
	public function getFileIds() : array {
		return $this->files->map( function ( $file ) {
			return $file->getId();
		} )->toArray();
	}

	/**
	 * User Notices
	 *
	 * @return Collection
	 */
	public function getUserNotices() : Collection {
		return $this->userNotices;
	}

	/**
	 * Add User Notice
	 *
	 * @param User $user User
	 *
	 * @return self
	 */
	public function addUserNotice( User $user ) : self {
		$this->userNotices->add( $user );

		return $this;
	}

	/**
	 * Set User Notices
	 *
	 * @Groups({"api"})
	 *
	 * @param Collection $userNotices User Notices
	 *
	 * @return self
	 */
	public function setUserNotices( Collection $userNotices ) : self {
		$this->userNotices = $userNotices;

		return $this;
	}

	/**
	 * Remove User Notice
	 *
	 * @param User $userNotice User Notice
	 *
	 * @return self
	 */
	public function removeUserNotice( User $userNotice ) : self {
		$this->userNotices->remove( $userNotice );

		return $this;
	}

	/**
	 * Set User Notice Ids
	 *
	 * @param int[] $userNoticeIds User Ids
	 *
	 * @return Collection
	 */
	public function setUserNoticeIds( array $userNoticeIds ) : self {
		$this->userNotices = new ArrayCollection( array_map( function ( $id ) {
			return new User( [
				'id' => $id,
			] );
		}, $userNoticeIds ) );

		return $this;
	}

	/**
	 * User Notices Ids
	 *
	 * @Groups({"api"})
	 *
	 * @return Collection
	 */
	public function getUserNoticeIds() : array {
		return $this->userNotices->map( function ( $user ) {
			return $user->getId();
		} )->toArray();
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

	/**
	 * {@inheritdoc}
	 *
	 * @return array
	 */
	public function getGroupSequence() {
		$groups = [ 'Dmca' ];

		if ( $this->lumenSend ) {
			$groups[] = 'Lumen';
		}

		return $groups;
	}
}
