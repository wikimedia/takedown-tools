<?php

namespace App\Client;

use App\Entity\Takedown\Takedown;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Ncme Client.
 */
interface NcmeClientInterface {

	/**
	 * Post Lumen Notice
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function createReport( Takedown $takedown ) : PromiseInterface;

}
