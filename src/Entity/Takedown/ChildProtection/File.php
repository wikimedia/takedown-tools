<?php

namespace App\Entity\Takedown\ChildProtection;

use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityAttacher\Annotation\Attach;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown_cp_file")
 *
 * @Assert\GroupSequenceProvider
 */
class File implements GroupSequenceProviderInterface {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="file_id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	private $id;

	/**
	 * @var ChildProtection
	 *
	 * @ORM\ManyToOne(
	 *	targetEntity="App\Entity\Takedown\ChildProtection\ChildProtection",
	 *	inversedBy="files"
	 *)
	 * @ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")
	 * @Attach()
	 */
	private $cp;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="ncmec_id", type="string", length=63, nullable=true)
	 */
	private $ncmecId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ip", type="string", length=15, nullable=true)
	 */
	private $ip;

	/**
	 * @var \DateTimeInterface
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $uploaded;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="exif", type="json_array", nullable=true)
	 */
	private $exif;

	/**
	 * Takedown
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->id = $params->getInt( 'id' );
		$this->dmca = $params->getInstance( 'cp', ChildProtection::class, new ChildProtection() );
		$this->name = $params->getString( 'name' );
		$this->ip = $params->getString( 'ip' );
		$this->uploaded = $params->getInstance( 'uploaded', \DateTime::class );
		$this->exif = $params->getArray( 'exif' );
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
	 * Set Child Proection
	 *
	 * @param ChildProtection $cp Child Proection
	 *
	 * @return self
	 */
	public function setCp( ChildProtection $cp ) : self {
		$this->cp = $cp;

		return $this;
	}

	/**
	 * Get Child Proection
	 *
	 * @return ChildProtection
	 */
	public function getCp() :? ChildProtection {
		return $this->cp;
	}

	/**
	 * Set NCMEC Id
	 *
	 * @param string|null $ncmecId NCMEC Id
	 *
	 * @return self
	 */
	public function setNcmecId( ?string $ncmecId ) : self {
		$this->ncmecId = $ncmecId;

		return $this;
	}

	/**
	 * NCMEC Id
	 *
	 * @Groups({"api"})
	 *
	 * @return bool
	 */
	public function getNcmecId() :? string {
		return $this->ncmecId;
	}

	/**
	 * Set Name.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $name File Name
	 *
	 * @return self
	 */
	public function setName( string $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get Name
	 *
	 * @Groups({"api"})
	 * @Assert\Length(max=255)
	 * @Assert\NotBlank(groups={"Approved"})
	 *
	 * @return string
	 */
	public function getName() :? string {
		return $this->name;
	}

	/**
	 * Set Ip.
	 *
	 * @Groups({"api"})
	 *
	 * @param string $ip IP Address
	 *
	 * @return self
	 */
	public function setIp( string $ip ) {
		$this->ip = $ip;

		return $this;
	}

	/**
	 * Get IP Address
	 *
	 * @Groups({"api"})
	 * @Assert\Ip()
	 * @Assert\NotBlank(groups={"Approved"})
	 *
	 * @return string
	 */
	public function getIp() :? string {
		return $this->ip;
	}

	/**
	 * Set uploaded
	 *
	 * @Groups({"api"})
	 *
	 * @param \DateTimeInterface $uploaded Uploaded
	 *
	 * @return self
	 */
	public function setUploaded( \DateTimeInterface $uploaded ) : self {
			$this->uploaded = $uploaded;

			return $this;
	}

	/**
	 * Get created
	 *
	 * @Groups({"api"})
	 * @Assert\NotNull(groups={"Approved"})
	 *
	 * @return \DateTime
	 */
	public function getUploaded() :? \DateTimeInterface {
			return $this->uploaded;
	}

	/**
	 * Set Exif.
	 *
	 * @Groups({"api"})
	 *
	 * @param array $exif Exif Data
	 *
	 * @return self
	 */
	public function setExif( array $exif ) {
		$this->exif = $exif;

		return $this;
	}

	/**
	 * Get Exif
	 *
	 * @Groups({"api"})
	 *
	 * @return array
	 */
	public function getExif() :? array {
		return $this->exif;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array
	 */
	public function getGroupSequence() {
		$groups = [ 'File' ];

		if ( $this->getCp() && $this->getCp()->isApproved() ) {
			$groups[] = 'Approved';
		}

		return $groups;
	}
}
