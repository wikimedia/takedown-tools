<?php

namespace App\Client;

use App\Entity\Takedown\Takedown;
use App\Entity\Takedown\ChildProtection\File;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * NCMEC Client.
 */
class NcmecClient implements NcmecClientInterface {

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
	 * Create NCMEC Report
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
			$data = $this->normalizer->normalize( $takedown, 'ncmec' );
			$xml = $this->encoder->encode( $data, 'xml', [
				'xml_root_node_name' => 'report',
			] );

			return $this->client->requestAsync( 'POST', 'submit', [
				'body' => $xml,
				'headers' => [
					'Content-Type' => 'text/xml; charset=utf-8',
				],
			] )->then( function ( $response ) {
				$data = $this->decoder->decode( (string)$response->getBody(), 'xml' );

				$reportId = intval( $data['reportId'] ?? 0 );
				return $reportId ?? null;
			} );
		} );
	}

	/**
	 * Send NCMEC File
	 *
	 * @param Takedown $takedown Takedown
	 * @param File $file File
	 * @param resource $content Content Stream
	 *
	 * @return PromiseInterface
	 */
	public function sendFile( Takedown $takedown, File $file, $content ) : PromiseInterface {
		if ( !is_resource( $content ) ) {
			throw new \InvalidArgumentException( 'Content must be a resource' );
		}

		$file = clone $file;

		return $this->client->requestAsync( 'POST', 'upload', [
			'multipart' => [
				[
					'name' => 'id',
					'contents' => $takedown->getCp()->getNcmecId(),
				],
				[
					'name' => 'file',
					'contents' => $content,
					'filename' => $file->getName(),
				],
			],
		] )->then( function ( $response ) use ( $file ) {
			$data = $this->decoder->decode( (string)$response->getBody(), 'xml' );

			// Do not modify the original file.
			$file->setNcmecId( $data['fileId'] ?? null );

			$data = $this->normalizer->normalize( $file, 'ncmec' );
			$xml = $this->encoder->encode( $data, 'xml', [
				'xml_root_node_name' => 'fileDetails',
			] );

			return $this->client->requestAsync( 'POST', 'fileinfo', [
				'body' => $xml,
				'headers' => [
					'Content-Type' => 'text/xml; charset=utf-8'
				]
			] )->then( function ( $response ) use ( $file ) {
				return $file;
			} );
		} );
	}

	/**
	 * Finish NCMEC Report
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function finishReport( Takedown $takedown ) : PromiseInterface {
		return $this->client->requestAsync( 'POST', 'finish', [
			'form_params' => [
				'id' => $takedown->getCp()->getNcmecId()
			],
		] )->then( function ( $response ) {
			return $this->decoder->decode( (string)$response->getBody(), 'xml' );
		} );
	}

	/**
	 * Retract NCMEC Report
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function retractReport( Takedown $takedown ) : PromiseInterface {
		return $this->client->requestAsync( 'POST', 'retract', [
			'form_params' => [
				'id' => $takedown->getCp()->getNcmecId()
			],
		] )->then( function ( $response ) {
			$data = $this->decoder->decode( (string)$response->getBody(), 'xml' );

			return $data;
		} );
	}
}
