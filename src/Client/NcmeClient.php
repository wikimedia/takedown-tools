<?php

namespace App\Client;

use App\Entity\Takedown\Takedown;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Ncme Client.
 */
class NcmeClient implements NcmeClientInterface {

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
	 * @var DecoderInterface
	 */
	protected $decoder;

	/**
	 * Lumen Client
	 *
	 * @param ClientInterface $client Guzzle Client
	 * @param MediaWikiClientInterface $mediaWikiClient MediaWiki Client
	 * @param NormalizerInterface $normalizer Normalizer
	 * @param EncoderInterface $encoder Encoder
	 * @param DecoderInterface $decoder Decoder
	 */
	public function __construct(
		ClientInterface $client,
		MediaWikiClientInterface $mediaWikiClient,
		NormalizerInterface $normalizer,
		EncoderInterface $encoder,
		DecoderInterface $decoder
	) {
		$this->client = $client;
		$this->mediaWikiClient = $mediaWikiClient;
		$this->normalizer = $normalizer;
		$this->encoder = $encoder;
		$this->decoder = $decoder;
	}

	/**
	 * Post Lumen Notice
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function createReport( Takedown $takedown ) : PromiseInterface {
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

			return $this->client->requestAsync( 'POST', 'submit', [
				'body' => $xml,
			] )->then( function ( $response ) {
				$data = $this->decoder->decode( (string)$response->getBody(), 'xml' );

				$reportId = intval( $data['reportId'] ?? 0 );
				return $reportId ?? null;
			} );
		} );
	}

}
