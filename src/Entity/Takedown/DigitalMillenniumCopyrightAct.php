<?php

namespace App\Entity\Takedown;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="takedown_dmca")
 *
 * @TODO add validation.
 */
class DigitalMillenniumCopyrightAct {

	/**
	 * @var Takedown
	 *
	 * @ORM\Id
	 * @ORM\OneToOne(targetEntity="App\Entity\Takedown\Takedown")
   * @ORM\JoinColumn(name="takedown_id", referencedColumnName="takedown_id")
	 */
	private $id;

}
