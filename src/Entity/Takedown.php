<?php

namespace App\Entity;

use App\EntityUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown")
 *
 * @TODO add validation.
 */
class Takedown {

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
	 * User
	 *
	 * @param array $data Data to construct the object.
	 */
	public function __construct( array $data = [] ) {
		$id = $data['id'] ?? null;
		$this->id = is_int( $id ) ? $id : null;

		// @TODO Use a library for this.
		$reporter = $data['reporter'] ?? null;
		if ( $reporter instanceof User ) {
			$this->reporter = $reporter;
		} elseif ( is_array( $reporter ) ) {
			$this->reporter = new User( $reporter );
		} else {
			$this->reporter = null;
		}

		$project = $data['project'] ?? null;
		if ( $project instanceof User ) {
			$this->project = $project;
		} elseif ( is_array( $project ) ) {
			$this->project = new Project( $project );
		} else {
			$this->project = null;
		}
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

}
