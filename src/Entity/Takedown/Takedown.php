<?php

namespace App\Entity\Takedown;

use App;
use App\Entity\Site;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Metadata;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\Parameter;
use GeoSocio\EntityUtils\CreatedTrait;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown")
 * @ORM\HasLifecycleCallbacks
 *
 * @TODO add validation.
 */
class Takedown {

	use CreatedTrait;

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
   * @ORM\JoinColumn(name="reporter_id", referencedColumnName="user_id")
	 */
	private $reporter;

	/**
	 * @var Site
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Site")
   * @ORM\JoinColumn(name="site_id", referencedColumnName="site_id")
	 */
	private $site;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToMany(targetEntity="App\Entity\User")
	 * @ORM\JoinTable(name="takedown_involved",
	 *      joinColumns={@ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(
	 *        name="user_id",
	 *        referencedColumnName="user_id"
	 *      )}
	 * )
	 */
	private $involved;

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
	 */
	private $metadata;

	/**
	 * @var DigitalMillenniumCopyrightAct
	 *
	 * @ORM\OneToOne(targetEntity="App\Entity\Takedown\DigitalMillenniumCopyrightAct", mappedBy="id")
	 */
	private $dmca;

	/**
	 * @var ChildProtection
	 *
	 * @ORM\OneToOne(targetEntity="App\Entity\Takedown\ChildProtection", mappedBy="id")
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
		$this->project = $params->getInstance( 'project', Project::class );
		$this->involved = $params->getCollection( 'involved', User::class, new ArrayCollection() );
		$this->metadata = $params->getCollection( 'involved', Metadata::class, new ArrayCollection() );
		$this->dmca = $params->getInstance( 'dmca', DigitalMillenniumCopyrightAct::class );
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
	 * @Groups({"autoconfirmed"})
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
	 * @Groups({"autoconfirmed"})
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
	 * @Groups({"autoconfirmed"})
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
	 * @Groups({"autoconfirmed"})
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
	 * @Groups({"autoconfirmed"})
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
	 * @Groups({"autoconfirmed"})
	 *
	 * @return Collection
	 */
	public function getInvolvedIds() : array {
		return $this->involved->map( function ( $user ) {
			return $user->getId();
		} )->toArray();
	}

	/**
	 * Set Involved User Id
	 *
	 * @Groups({"autoconfirmed"})
	 *
	 * @param array $userIds Involved User Ids
	 *
	 * @return self
	 */
	public function setInvolvedIds( array $userIds ) : self {
		$users = array_map( function ( $id ) {
			return new User( [
				'id' => $id,
			] );
		}, $userIds );

		$this->involved = new ArrayCollection( $users );

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
	 * @Groups({"autoconfirmed"})
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
	 * @Groups({"autoconfirmed"})
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
	 * @param DigitalMillenniumCopyrightAct $dmca DMCA
	 *
	 * @return self
	 */
	public function setDmca( DigitalMillenniumCopyrightAct $dmca ) : self {
		$this->dmca = $dmca;

		return $this;
	}

	/**
	 * DMCA
	 *
	 * @return DigitalMillenniumCopyrightAct
	 */
	public function getDmca() :? DigitalMillenniumCopyrightAct {
		return $this->dmca;
	}

	/**
	 * Set Child Protection.
	 *
	 * @param ChildProtection $cp Child Protection
	 *
	 * @return self
	 */
	public function setCp( ChildProtection $cp ) : self {
		$this->cp = $cp;

		return $this;
	}

	/**
	 * Child Protection
	 *
	 * @return ChildProtection
	 */
	public function getCp() :? ChildProtection {
		return $this->cp;
	}

}
