<?php

namespace App\Client;

use App\Entity\Takedown\Takedown;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Lumen Client Interface
 */
interface LumenClientInterface {

	/**
	 * Post Lumen Notice
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function postNotice( Takedown $takedown ) : PromiseInterface;
}
