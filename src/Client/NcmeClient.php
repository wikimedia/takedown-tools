<?php

namespace App\Client;

use App\Entity\Takedown\Takedown;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Ncme Client.
 */
class NcmeClient {

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
	 * @var EncoderInterface
	 */
	protected $encoder;

	/**
	 * Lumen Client
	 *
	 * @param ClientInterface $client Guzzle Client
	 * @param MediaWikiClientInterface $mediaWikiClient MediaWiki Client
	 * @param NormalizerInterface $normalizer Normalizer
	 * @param EncoderInterface $encoder Encoder
	 */
	public function __construct(
		ClientInterface $client,
		MediaWikiClientInterface $mediaWikiClient,
		NormalizerInterface $normalizer,
		EncoderInterface $encoder
	) {
		$this->client = $client;
		$this->mediaWikiClient = $mediaWikiClient;
		$this->normalizer = $normalizer;
		$this->encoder = $encoder;
	}

	/**
	 * Post Lumen Notice
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function postReport( Takedown $takedown ) : PromiseInterface {
		$promise = new FulfilledPromise( $takedown );

		// Get the site with info.
		if ( $takedown->getSite() ) {
			$promise = $this->mediaWikiClient->getSite( $takedown->getSite()->getId() )
				->then( function ( $site ) use ( $takedown ) {
					$takedown->setSite( $site );

					if ( $site->getInfo() ) {
						return $takedown;
					}

					return $this->mediaWikiClient->getSiteInfo( $site )
						->then( function ( $info ) use ( $takedown ) {
							$takedown->getSite()->setInfo( $info );
							return $takedown;
						} );
				} );
		}

		return $promise->then( function ( $takedown ) {
			$data = $this->normalizer->normalize( $takedown, 'ncme' );
			$xml = $this->encoder->encode( $data, 'xml', [
				'xml_root_node_name' => 'report',
			] );

			dump( $xml );
			exit;

			return $this->client->requestAsync( 'POST', [
				'body' => $xml,
			] )->then( function ( $response ) {
				// @TODO Do something with the response!
				dump( $response );
				exit;
			} );
		} );
	}

}
