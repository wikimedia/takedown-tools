<?php

namespace App\Util;

use App\Entity\Takedown\Takedown;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Takedown Utility.
 */
interface TakedownUtilInterface {

	/**
	 * Creates a Takedown
	 *
	 * @param Takedown $takedown Takedown.
	 *
	 * @return PromiseInterface
	 */
	public function create( Takedown $takedown ) : PromiseInterface;

	/**
	 * Deletes a Takedown
	 *
	 * @param Takedown $takedown Takedown.
	 *
	 * @return PromiseInterface
	 */
	public function delete( Takedown $takedown ) : PromiseInterface;

}
