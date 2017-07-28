<?php

namespace App\Serializer;

use App\Entity\Takedown\Takedown;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Ncme Normalizer
 */
class NcmeNormalizer implements NormalizerInterface {

	/**
	 * @var array
	 */
	protected $organization;

	/**
	 * @var array
	 */
	protected $contact;

	/**
	 * Lumen Normalizer
	 *
	 * @param array $organization Organization.
	 * @param array $contact Contact.
	 */
	public function __construct( array $organization, array $contact ) {
		$this->organization = $organization;
		$this->contact = $contact;
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
		$street = $this->organization['address'][0] ?? null;
		if ( $street && isset( $this->organization['address'][1] ) ) {
			$street .= ' ' . $this->organization['address'][1];
		}

		$address = [
			'@type' => 'Business',
			'#' => [
				'address' => $street ?? null,
				'city' => $this->organization['city'] ?? null,
				'state' => $this->organization['state'] ?? null,
				'country' => $this->organization['country'] ?? null,
				'zipCode' => $this->organization['zip'] ?? null,
			],
		];

		$data = [
			'incidentSummary' => [
				// @FIXME ASSUMPTION, not even asking yet
				'incidentType' => 'Child Pornography (possession, manufacture, and distribution)',
			],
			'internetDetails' => [],
			'reporter' => [
				'reportingPerson' => [
					'address' => $address,
				],
				'contactPerson' => [
					'firstName' => $this->contact['name']['first'] ?? null,
					'lastName' => $this->contact['name']['last'] ?? null,
					'phone' => [
						'@type' => 'Business',
						'#' => $this->contact['phone'] ?? null
					],
					'address' => $address,
				],
			],
			'personOrUserReported' => [
				'additionalInfo' => $object->getCp()->getComments()
			],
		];

		// Get the Reporter's Email.
		if ( $object->getReporter()->getEmail() ) {
			$data['reporter']['reportingPerson']['email'] = [
				'@verified' => $object->getReporter()->isEmailVerified(),
				'#' => $object->getReporter()->getEmail(),
			];
		}

		// Sort the files by uploaded date.
		$criteria = Criteria::create()
			->orderBy( [ 'uploaded' => 'DESC' ] );
		$files = $object->getCp()->getFiles()->matching( $citeria );

		// Use the first file's date as the incident date.
		if ( $files->count() ) {
			$file = $files->first();
			$data['incidentSummary']['incidentDateTime'] = $file->getUploaded()->format( 'c' );
		}

		// Add the page urls.
		if ( $object->getPages()->count() ) {
			$urls = $object->getPages()->map( function( $page ) use ( $object ) {
				return $page->getUrl( $object->getSite() );
			} )->toArray();

			$data['internetDetails']['webPageIncident']['url'] = $urls;
		}

		// Add the reported user's screen name.
		if ( $object->getInvovled()->count() ) {
			$user = $object->getInvolved()->first();
			$data['personOrUserReported']['screenName'] = $user->getUsername();

			if ( $object->getSite() ) {
				$url = $user->getUrl( $object->getSite() );
				$data['personOrUserReported']['profileUrl'] = $url;
			}
		}

		// Get the IP Address of all of the uploaded files.
		$capture = $object->getCp()->getFiles()->map( function ( $file ) {
			return [
				'ipAddress' => $file->getIp(),
				'eventName' => 'Upload',
				'dateTime' => $file->getUploaded() ? $file->getUploaded()->format( 'c' ) : null,
			];
		} )->toArray();

		$data['personOrUserReported']['ipCaptureEvent'] = $capture;

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
		return $format === 'ncme' && $data instanceof Takedown && $data->getCp();
	}
}
