<?php

namespace App\Client;

use App\Entity\Takedown\Takedown;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Ncme Client.
 */
interface NcmecClientInterface {

	/**
	 * Post Lumen Notice
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function createReport( Takedown $takedown ) : PromiseInterface;

}
