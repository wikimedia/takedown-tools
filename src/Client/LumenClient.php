<?php

namespace App\Client;

use App\Entity\Takedown\Takedown;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Lumen Client.
 */
class LumenClient implements LumenClientInterface {

	/**
	 * @var ClientInterface
	 */
	protected $client;

	/**
	 * @var MediaWikiClientInterface
	 */
	protected $mediaWikiClient;

	/**
	 * @var NormalizerInterface
	 */
	protected $normalizer;

	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var string
	 */
	protected $filesDir;

	/**
	 * Lumen Client
	 *
	 * @param ClientInterface $client Guzzle Client
	 * @param MediaWikiClientInterface $mediaWikiClient MediaWiki Client
	 * @param NormalizerInterface $normalizer Normalizer
	 * @param string $token Authentication Token
	 * @param string $filesDir Files Directory.
	 */
	public function __construct(
		ClientInterface $client,
		MediaWikiClientInterface $mediaWikiClient,
		NormalizerInterface $normalizer,
		string $token,
		string $filesDir
	) {
		$this->client = $client;
		$this->mediaWikiClient = $mediaWikiClient;
		$this->normalizer = $normalizer;
		$this->token = $token;
		$this->filesDir = $filesDir;
	}

	/**
	 * Post Lumen Notice
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function postNotice( Takedown $takedown ) : PromiseInterface {
		$promise = new FulfilledPromise( $takedown );

		if ( $takedown->getSite() ) {
			$promise = $this->mediaWikiClient->getSite( $takedown->getSite()->getId() )->then(
				function ( $site ) use ( $takedown ) {
					$takedown->setSite( $site );
					return new FulfilledPromise( $takedown );
				}
			);
		}

		return $promise->then( function ( $takedown ) {
			$options = [
				'form_params' => [
					'notice' => $this->normalizer->normalize( $takedown, 'lumen' ),
					'authentication_token' => $this->token,
				],
			];

			$options['multipart'] = $takedown->getDmca()->getFiles()->map( function( $file ) {
				return [
					'name' => 'notice[file_uploads_attributes]['
						. $takedown->getDmca()->getFiles()->indexOf( $file ) . '][file]',
					'filename' => $file->getName(),
					'contents' => fopen( $this->filesDir . '/' . $file->getPath(), 'r' )
				];
			} )->toArray();

			dump( $options );
			exit;

			return $this->client->requestAsync( 'POST', '/notices', $options );
		} );
	}
}
