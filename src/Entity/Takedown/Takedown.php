<?php

namespace App\Entity\Takedown;

use App\Entity\Site;
use App\Entity\User;
use App\Entity\Metadata;
use App\Entity\Takedown\Page;
use App\Entity\Takedown\Dmca\Dmca;
use App\Entity\Takedown\ChildProtection\ChildProtection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityAttacher\Annotation\Attach;
use GeoSocio\EntityUtils\CreatedTrait;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown")
 * @ORM\HasLifecycleCallbacks
 *
 * @Assert\GroupSequenceProvider
 */
class Takedown implements GroupSequenceProviderInterface {

	use CreatedTrait;

	/**
	 * @var string
	 */
	const TYPE_DMCA = 'dmca';

	/**
	 * @var string
	 */
	const TYPE_CP = 'cp';

	/**
	 * @var int
	 *
	 * @ORM\Column(name="takedown_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	private $id;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(name="reporter", referencedColumnName="user_id")
	 * @Attach()
	 */
	private $reporter;

	/**
	 * @var Site
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Site", cascade={"persist"})
	 * @ORM\JoinColumn(name="site", referencedColumnName="site_id")
	 * @Attach()
	 */
	private $site;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", cascade={"persist"})
	 * @ORM\JoinTable(name="takedown_involved",
	 *      joinColumns={@ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(
	 *        name="user_id",
	 *        referencedColumnName="user_id"
	 *      )}
	 * )
	 * @Attach()
	 */
	private $involved;

	/**
	 * @var Collection
	 *
	 * @ORM\OneToMany(
	 * 	targetEntity="App\Entity\Takedown\Page",
	 * 	mappedBy="takedown",
	 * 	cascade={"persist", "remove"}
	 *)
	 */
	private $pages;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToMany(targetEntity="App\Entity\Metadata")
	 * @ORM\JoinTable(name="takedown_metadata",
	 *      joinColumns={@ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(
	 *        name="metadata_id",
	 *        referencedColumnName="metadata_id"
	 *      )}
	 * )
	 * @Attach()
	 */
	private $metadata;

	/**
	 * @var Dmca
	 *
	 * @ORM\OneToOne(
	 *	targetEntity="App\Entity\Takedown\Dmca\Dmca",
	 *	mappedBy="takedown",
	 *	orphanRemoval=true,
	 *	cascade={"persist", "remove"}
	 *)
	 * @Attach()
	 */
	private $dmca;

	/**
	 * @var ChildProtection
	 *
	 * @ORM\OneToOne(
	 *	targetEntity="App\Entity\Takedown\ChildProtection\ChildProtection",
	 *	mappedBy="takedown",
	 *	orphanRemoval=true,
	 *  cascade={"persist", "remove"}
	 *)
	 * @Attach()
	 */
	private $cp;

	/**
	 * Takedown
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->id = $params->getInt( 'id' );
		$this->reporter = $params->getInstance( 'reporter', User::class );
		$this->involved = $params->getCollection( 'involved', User::class, new ArrayCollection() );
		$this->metadata = $params->getCollection( 'metadata', Metadata::class, new ArrayCollection() );
		$this->pages = $params->getCollection(
			'pages',
			Page::class,
			new ArrayCollection()
		);
		$this->created = $params->getInstance( 'created', \DateTime::class );
		$this->dmca = $params->getInstance( 'dmca', Dmca::class );
		$this->cp = $params->getInstance( 'cp', ChildProtection::class );
	}

	/**
	 * Set Id.
	 *
	 * @param int $id ID
	 *
	 * @return self
	 */
	public function setId( int $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Get Id
	 *
	 * @Groups({"api"})
	 *
	 * @return int
	 */
	public function getId() :? int {
		return $this->id;
	}

	/**
	 * Set Reporter.
	 *
	 * @param User $reporter Reporter
	 *
	 * @return self
	 */
	public function setReporter( User $reporter ) {
		$this->reporter = $reporter;

		return $this;
	}

	/**
	 * Get Reporter
	 *
	 * @return User
	 */
	public function getReporter() :? User {
		return $this->reporter;
	}

	/**
	 * Get Reporter
	 *
	 * @Groups({"api"})
	 * @Assert\NotNull()
	 *
	 * @return int
	 */
	public function getReporterId() :? int {
		if ( $this->reporter ) {
			return $this->reporter->getId();
		}

		return null;
	}

	/**
	 * Get Reporter Id
	 *
	 * @Groups({"api"})
	 *
	 * @param int $reporterId Reporter Id.
	 *
	 * @return User
	 */
	public function setReporterId( int $reporterId ) : self {
		$this->reporter = new User( [
			'id' => $reporterId,
		] );

		return $this;
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
	 * @Assert\NotNull(groups={"Lumen"})
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
	 * @param string $siteId Site Id.
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
	 * Involved Users
	 *
	 * @return Collection
	 */
	public function getInvolved() : Collection {
		return $this->involved;
	}

	/**
	 * Add Involved User
	 *
	 * @param User $user Involved User
	 *
	 * @return self
	 */
	public function addInvolved( User $user ) : self {
		$this->involved->add( $user );

		return $this;
	}

	/**
	 * Set Involved Users
	 *
	 * @param User[] $users Involved Users
	 *
	 * @return self
	 */
	public function setInvolved( iterable $users ) : self {
		if ( $users instanceof Collection ) {
			$this->involved = $users;
		} else {
			$this->involved = new ArrayCollection( $users );
		}

		return $this;
	}

	/**
	 * Remove Involved User
	 *
	 * @param User $user Involved User
	 *
	 * @return self
	 */
	public function removeInvolved( User $user ) : self {
		$this->involved->remove( $user );

		return $this;
	}

	/**
	 * Involved Users Ids
	 *
	 * @Groups({"api"})
	 *
	 * @return Collection
	 */
	public function getInvolvedIds() : array {
		return $this->involved->map( function ( $user ) {
			return $user->getId();
		} )->toArray();
	}

	/**
	 * Involved Users Names
	 *
	 * @return Collection
	 */
	public function getInvolvedNames() : array {
		return $this->involved->map( function ( $user ) {
			return $user->getUsername();
		} )->toArray();
	}

	/**
	 * Set Involved User Id
	 *
	 * @Groups({"api"})
	 *
	 * @param array $userNames Involved User Ids
	 *
	 * @return self
	 */
	public function setInvolvedNames( array $userNames ) : self {
		$users = array_map( function ( $name ) {
			return new User( [
				'username' => $name,
			] );
		}, $userNames );

		$this->involved = new ArrayCollection( $users );

		return $this;
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
				'takedown' => $this,
			] );
		}, $pageIds ) );

		return $this;
	}

	/**
	 * Metadata
	 *
	 * @return Collection
	 */
	public function getMetadata() : Collection {
		return $this->metadata;
	}

	/**
	 * Add Metadata
	 *
	 * @param Metadata $metadata Metadata User
	 *
	 * @return self
	 */
	public function addMetadata( Metadata $metadata ) : self {
		$this->metadata->add( $metadata );

		return $this;
	}

	/**
	 * Remove Metadata
	 *
	 * @param Metadata $metadata Metadata
	 *
	 * @return self
	 */
	public function removedMetadata( Metadata $metadata ) : self {
		$this->metadata->remove( $metadata );

		return $this;
	}

	/**
	 * Metadata Ids
	 *
	 * @Groups({"api"})
	 *
	 * @return Collection
	 */
	public function getMetadataIds() : array {
		return $this->metadata->map( function ( $metadata ) {
			return $metadata->getId();
		} )->toArray();
	}

	/**
	 * Set Involved User Id
	 *
	 * @Groups({"api"})
	 *
	 * @param array $metadataIds Metadata Ids
	 *
	 * @return self
	 */
	public function setMetadataIds( array $metadataIds ) : self {
		$metadata = array_map( function ( $id ) {
			return new Metadata( [
				'id' => $id,
			] );
		}, $metadataIds );

		$this->metadata = new ArrayCollection( $metadata );

		return $this;
	}

	/**
	 * Set DMCA.
	 *
	 * @Groups({"api"})
	 *
	 * @param Dmca $dmca DMCA
	 *
	 * @return self
	 */
	public function setDmca( Dmca $dmca = null ) : self {
		$this->dmca = $dmca;
		if ( $this->dmca ) {
			$this->dmca->setTakedown( $this );
		}

		return $this;
	}

	/**
	 * Returns the type of takedown.
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getType() :? string {
		if ( $this->dmca && $this->cp ) {
			return null;
		}

		if ( !$this->dmca && !$this->cp ) {
			return null;
		}

		if ( $this->dmca ) {
			return self::TYPE_DMCA;
		}

		if ( $this->cp ) {
			return self::TYPE_CP;
		}
	}

	/**
	 * DMCA
	 *
	 * @Groups({"api"})
	 * @Assert\Valid()
	 *
	 * @return Dmca
	 */
	public function getDmca() :? Dmca {
		return $this->dmca;
	}

	/**
	 * Set Child Protection.
	 *
	 * @Groups({"api"})
	 *
	 * @param ChildProtection $cp Child Protection
	 *
	 * @return self
	 */
	public function setCp( $cp = null ) : self {
		if ( $cp === null ) {
			$this->cp = null;

			return $this;
		}

		if ( !$cp instanceof ChildProtection && !is_array( $cp ) ) {
			throw new \InvalidArgumentException( 'Cannot set CP' );
		}

		if ( is_array( $cp ) ) {
			$cp = new ChildProtection( $cp );
		}

		$this->cp = $cp;
		$this->cp->setTakedown( $this );

		return $this;
	}

	/**
	 * Child Protection
	 *
	 * @Groups({"api"})
	 * @Assert\Valid()
	 *
	 * @return ChildProtection
	 */
	public function getCp() :? ChildProtection {
		return $this->cp;
	}

	/**
	 * Get created
	 *
	 * @Groups({"api"})
	 *
	 * @return \DateTime
	 */
	public function getCreated() :? \DateTimeInterface {
			return $this->created;
	}

	/**
	 * Clone
	 *
	 * @return Takedown
	 */
	public function __clone() {
		$this->pages = $this->pages->map( function( $page ) {
			return new Page( [
				'key' => $page->getKey(),
				'takedown' => $this,
			] );
		} );

		if ( $this->dmca ) {
			$this->dmca = clone $this->dmca;
			$this->dmca->setTakedown( $this );
		}

		if ( $this->cp ) {
			$this->cp = clone $this->cp;
			$this->cp->setTakedown( $this );
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array
	 */
	public function getGroupSequence() {
		$groups = [ 'Takedown' ];

		if ( $this->dmca && $this->dmca->getLumenSend() ) {
			$groups[] = 'Lumen';
		}

		return $groups;
	}
}
