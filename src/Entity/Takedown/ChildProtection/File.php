<?php

namespace App\Entity\Takedown\ChildProtection;

use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityAttacher\Annotation\Attach;
use GeoSocio\EntityUtils\ParameterBag;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown_cp_file")
 *
 * @todo add validation.
 */
class File {

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
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 */
	private $name;

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
		$this->exif = $params->getArray( 'exif' );
	}

	/**
	 * Set Id.
	 *
	 * @param string $id ID
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
	 *
	 * @return string
	 */
	public function getName() :? string {
		return $this->name;
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
}
