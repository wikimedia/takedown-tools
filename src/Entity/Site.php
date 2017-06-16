<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use GeoSocio\EntityUtils\ParameterBag;

/**
 * @ORM\Entity
 * @ORM\Table(name="site")
 *
 * @TODO add validation.
 */
class Site {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="site_id", type="string", length=31)
	 * @ORM\Id
	 */
	private $id;

	/**
	 * @var Project
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Project")
   * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
	 */
	private $project;

	/**
	 * Site
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$params = new ParameterBag( $data );
		$this->id = $params->getString( 'id' );
		$this->project = $params->getInstance( 'project', Project::class );
	}

	/**
	 * Set Id.
	 *
	 * @param string $id ID
	 *
	 * @return self
	 */
	public function setId( string $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Get Id
	 *
	 * @return string
	 */
	public function getId() :? string {
		return $this->id;
	}

	/**
	 * Set Project.
	 *
	 * @param Project $project Project
	 *
	 * @return self
	 */
	public function setProject( Project $project ) {
		$this->project = $project;

		return $this;
	}

	/**
	 * Get Project
	 *
	 * @return Project
	 */
	public function getProject() :? Project {
		return $this->project;
	}

}
