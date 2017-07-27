<?php

namespace App\Serializer;

use App\Entity\Takedown\Takedown;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Lumen Normalizer
 */
class LumenNormalizer implements NormalizerInterface {

	/**
	 * @var string
	 */
	protected $environment;

	/**
	 * Lumen Normalizer
	 *
	 * @param string $environment Environment.
	 */
	public function __construct(
		string $environment
	) {
		$this->environment = $environment;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param object $object Object to normalize
	 * @param string $format Format the normalization result will be encoded as
	 * @param array $context Context options for the normalizer
	 *
	 * @return array
	 */
	public function normalize( $object, $format = null, array $context = [] ) {
		$sender = [
			'name' => $object->getDmca()->getSenderName(),
			'city' => $object->getDmca()->getSenderCity(),
			'state' => $object->getDmca()->getSenderState(),
			'zip' => $object->getDmca()->getSenderZip(),
		];

		if ( $object->getDmca()->getSenderAddress() ) {
			$address = $object->getDmca()->getSenderAddress();
			if ( isset( $address[0] ) ) {
				$sender['address_line_1'] = $address[0];
			}
			if ( isset( $address[1] ) ) {
				$sender['address_line_2'] = $address[1];
			}
		}

		if ( $object->getDmca()->getSenderCountry() ) {
			$sender['country_code'] = $object->getDmca()->getSenderCountry()->getId();
		}

		$originals = $object->getDmca()->getOriginals()->map( function ( $original ) {
			return [
				'url' => $original->getUrl(),
			];
		} )->toArray();

		$infringing = $object->getPages()->map( function ( $page ) use ( $object ) {
			$site = $object->getSite();

			if ( !$site ) {
				return [
					'url' => '/' . $page->getKey(),
				];
			}

			$path = '/wiki/' . $page->getKey();

			if ( $site->getInfo() ) {
				$info = $site->getInfo();

				if ( !empty( $info['general']['articlepath'] ) ) {
					$template = $info['general']['articlepath'];
					$path = preg_replace( '/^(.*)$/', $template, $page->getKey() );
				}
			}

			return [
				'url' => 'https://' . $site->getDomain() . $path,
			];
		} )->toArray();

		$files = $object->getDmca()->getFiles()->map( function( $file ) {
			return [
				'kind' => 'original',
			];
		} )->toArray();

		$recipient = [
			'name' => 'Wikimedia Foundation',
			'kind' => 'organization',
			'address_line_1' => '149 New Montgomery St. 6th FL',
			'city' => 'San Francisco',
			'state' => 'CA',
			'zip' => '94105',
			'country_code' => 'US',
			'phone' => '4158396885'
		];

		$data = [
			'title' => $object->getDmca()->getLumenTitle(),
			'type' => $this->environment === 'prod' ? 'DMCA' : 'Other',
			'subject' => $object->getDmca()->getSubject(),
			'body' => $object->getDmca()->getBody(),
			'language' => 'en',
			'source' => $object->getDmca()->getMethod(),
			'tag_list' => 'wikipedia, wikimedia',
			'jurisidiction_list' => 'US, CA',
			'url_count' => $object->getPages()->count(),
			'works_attributes' => [
				[
					'copyrighted_urls_attributes' => $originals,
					'infringing_urls_attributes' => $infringing,
				],
			],
			'entity_notice_roles_attributes' => [
				[
					'name' => 'submitter',
					'entity_attributes' => $recipient,
				],
				[
					'name' => 'recipient',
					'entity_attributes' => $recipient,
				],
				[
					'name' => 'sender',
					'entity_attributes' => $sender,
				],
			],
			'file_uploads_attributes' => $files,
		];

		if ( $object->getDmca()->getSent() ) {
			$data['date_sent'] = $object->getDmca()->getSent()->format( 'Y-m-d' );
		}

		if ( $object->getDmca()->getActionTaken() ) {
			$data['action_taken'] = ucfirst( $object->getDmca()->getActionTaken()->getId() );
		}

		return $data;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param mixed $data Data to normalize
	 * @param string $format The format being (de-)serialized from or into
	 *
	 * @return bool
	 */
	public function supportsNormalization( $data, $format = null ) {
		return $format === 'lumen' && $data instanceof Takedown && $data->getDmca();
	}
}
