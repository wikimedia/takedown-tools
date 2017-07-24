<?php

namespace App\Client;

use App\Entity\Takedown\Takedown;
use GuzzleHttp\ClientInterface;
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
	protected $filesDir;

	/**
	 * Lumen Client
	 *
	 * @param ClientInterface $client Guzzle Client
	 * @param MediaWikiClientInterface $mediaWikiClient MediaWiki Client
	 * @param NormalizerInterface $normalizer Normalizer
	 * @param string $filesDir Files Directory.
	 */
	public function __construct(
		ClientInterface $client,
		MediaWikiClientInterface $mediaWikiClient,
		NormalizerInterface $normalizer,
		string $filesDir
	) {
		$this->client = $client;
		$this->mediaWikiClient = $mediaWikiClient;
		$this->normalizer = $normalizer;
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
					return $takedown;
				}
			);
		}

		return $promise->then( function ( $takedown ) {
			$multipart = $this->multipart( [
				'notice' => $this->normalizer->normalize( $takedown, 'lumen' ),
			] );

			$files = [];

			$takedown->getDmca()->getFiles()->forAll( function( $index, $file ) {
				$files[] = [
					'name' => 'notice[file_uploads_attributes][' . $index . '][file]',
					'filename' => $file->getName(),
					'contents' => fopen( $this->filesDir . '/' . $file->getPath(), 'r' )
				];
			} );

			$multipart = array_merge( $multipart, $files );

			return $this->client->requestAsync( 'POST', '/notices', [
					'multipart' => $multipart,
			] );
		} );
	}

	/**
	 * Flatten Form Params.
	 *
	 * @link https://stackoverflow.com/a/42660877/864374
	 *
	 * @param mixed $data Data to Flatten
	 * @param string $originalKey Key of Placement
	 *
	 * @return array
	 */
	protected function flatten( $data, string $originalKey = '' ) {
		$output = [];

		foreach ( $data as $key => $value ) {
			$newKey = $originalKey;

			if ( empty( $originalKey ) ) {
				$newKey .= $key;
			} else {
				$newKey .= '[' . $key . ']';
			}

			if ( is_array( $value ) ) {
				$output = array_merge( $output, $this->flatten( $value, $newKey ) );
			} else {
				$output[$newKey] = $value;
			}
		}

		return $output;
	}

	/**
	 * Convert Form Params to Multipart
	 *
	 * @link https://stackoverflow.com/a/42660877/864374
	 *
	 * @param mixed $data Data to Convert
	 *
	 * @return array
	 */
	protected function multipart( $data ) {
		$flat = $this->flatten( $data );
		$data = [];

		foreach ( $flat as $key => $value ) {
			$data[] = [
				'name'  => $key,
				'contents' => $value
		  ];
		}

		return $data;
	}
}
