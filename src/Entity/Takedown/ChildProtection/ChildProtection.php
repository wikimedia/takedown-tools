<?php

namespace App\Entity\Takedown\ChildProtection;

use App\Entity\Takedown\Takedown;
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
 * @ORM\Table(name="takedown_cp")
 *
 * @Assert\GroupSequenceProvider
 */
class ChildProtection implements GroupSequenceProviderInterface {

	/**
	 * @var Takedown
	 *
	 * @ORM\Id
	 * @ORM\OneToOne(targetEntity="App\Entity\Takedown\Takedown", inversedBy="cp")
	 * @ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")
	 */
	private $takedown;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="ncme_id", type="integer", nullable=true)
	 */
	private $ncmeId;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="approved", type="boolean", options={"default"=false})
	 */
	private $approved;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist"})
	 * @ORM\JoinColumn(name="approver", referencedColumnName="user_id")
	 * @Attach()
	 */
	private $approver;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="denied_approval_reason", type="string", length=255, nullable=true)
	 */
	 private $deniedApprovalReason;

	 /**
	 * @var \DateTimeInterface
	 *
	 * @ORM\Column(name="sent", type="datetime", nullable=true)
	 */
	private $accessed;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="comments", type="text", nullable=true)
	 */
	 private $comments;

	 /**
	 * @var Collection
	 *
	 * @ORM\OneToMany(
	 * 	targetEntity="App\Entity\Takedown\ChildProtection\File",
	 * 	mappedBy="cp",
	 * 	cascade={"persist", "remove"}
	 *)
	 */
	private $files;

	/**
	 * Takedown
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->takedown = $params->getInstance( 'takedown', Takedown::class, new Takedown() );
		$this->approved = $params->getBoolean( 'approved', false );
		$this->approver = $params->getInstance( 'approver', User::class );
		$this->deniedApprovalReason = $params->getString( 'deniedApprovalReason' );
		$this->accessed = $params->getInstance( 'accessed', \DateTime::class );
		$this->comments = $params->getString( 'comments' );
		$this->files = $params->getCollection( 'files', File::class, new ArrayCollection() );
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
	 * Set NCME Id
	 *
	 * @param int|null $ncmeId NCME Id
	 *
	 * @return self
	 */
	public function setNcmeId( ?int $ncmeId ) : self {
		$this->ncmeId = $ncmeId;

		return $this;
	}

	/**
	 * NCME Id
	 *
	 * @Groups({"api"})
	 *
	 * @return bool
	 */
	public function getNcmeId() :? int {
		return $this->ncmeId;
	}

	/**
	 * Approved
	 *
	 * @Groups({"api"})
	 *
	 * @return bool
	 */
	public function isApproved() : bool {
		return $this->approved;
	}

	/**
	 * Set Approved
	 *
	 * @Groups({"api"})
	 *
	 * @param bool $approved Approved
	 *
	 * @return self
	 */
	public function setApproved( bool $approved ) : self {
		$this->approved = $approved;

		return $this;
	}

	/**
	 * Approver
	 *
	 * @return User
	 */
	public function getApprover() :? User {
		return $this->approver;
	}

	/**
	 * Set Approver
	 *
	 * @param User $approver Approver
	 *
	 * @return self
	 */
	public function setApprover( User $approver ) : self {
		$this->approver = $approver;

		return $this;
	}

	/**
	 * Approver Ids
	 *
	 * @Groups({"api"})
	 *
	 * @return User
	 */
	public function getApproverId() :? int {
		if ( $this->approver ) {
			return $this->approver->getId();
		}

		return null;
	}

	/**
	 * Set Approver Name
	 *
	 * @Groups({"api"})
	 *
	 * @param string $approverName Approver Name.
	 *
	 * @return User
	 */
	public function setApproverName( string $approverName ) : self {
		$this->approver = new User( [
			'username' => $approverName,
		] );

		return $this;
	}

	/**
	 * Approver Name
	 *
	 * @Groups({"api"})
	 * @Assert\NotNull(groups={"Approved"})
	 *
	 * @return int
	 */
	public function getApproverName() :? string {
		if ( $this->approver ) {
			return $this->approver->getUsername();
		}

		return null;
	}

	/**
	 * Set Denied Approval Reason
	 *
	 * @Groups({"api"})
	 *
	 * @param string $deniedApprovalReason Denied Approval Reason
	 *
	 * @return self
	 */
	public function setDeniedApprovalReason( string $deniedApprovalReason ) : self {
		$this->deniedApprovalReason = $deniedApprovalReason;

		return $this;
	}

	/**
	 * Denied Approval Reason
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getDeniedApprovalReason() :? string {
		return $this->deniedApprovalReason;
	}

	/**
	 * Set sent
	 *
	 * @Groups({"api"})
	 *
	 * @param \DateTimeInterface $accessed The Accssesed DateTime.
	 *
	 * @return self
	 */
	public function setAccessed( \DateTimeInterface $accessed ) : self {
		$this->accessed = $accessed;

		return $this;
	}

	/**
	 * Sent
	 *
	 * @Groups({"api"})
	 * @Assert\NotNull(groups={"Approved"})
	 *
	 * @return \DateTime
	 */
	public function getAccessed() :? \DateTimeInterface {
		return $this->accessed;
	}

	/**
	 * Set Comments
	 *
	 * @Groups({"api"})
	 *
	 * @param string $comments Comments
	 *
	 * @return self
	 */
	public function setComments( string $comments ) : self {
		$this->comments = $comments;

		return $this;
	}

	/**
	 * Body
	 *
	 * @Groups({"api"})
	 *
	 * @return string
	 */
	public function getComments() :? string {
		return $this->comments;
	}

	/**
	 * Files
	 *
	 * @Groups({"api"})
	 *
	 * @param Collection $files Files
	 *
	 * @return Collection
	 */
	public function setFiles( Collection $files ) : self {
		$this->files = $files->map( function( $file ) {
			return $file->setCp( $this );
		} );

		return $this;
	}

	/**
	 * Files
	 *
	 * @Groups({"api"})
	 * @Assert\Valid()
	 * @Assert\Count(min=1, groups={"Approved"})
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
		$this->files->add( $file->setCp( $this ) );

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
	 * Clone
	 *
	 * @return void
	 */
	public function __clone() {
		$this->files = $this->files->map( function( $file ) {
			$file = clone $file;
			$file->setCp( $this );
			return $file;
		} );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array
	 */
	public function getGroupSequence() {
		$groups = [ 'ChildProtection' ];

		if ( $this->approved ) {
			$groups[] = 'Approved';
		}

		return $groups;
	}
}
