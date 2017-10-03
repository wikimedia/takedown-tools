<?php

namespace App\Serializer;

use App\Entity\Takedown\Takedown;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * NCMEC Normalizer
 */
class NcmecReportNormalizer implements NormalizerInterface {

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
	 * @param string|null $format Format the normalization result will be encoded as
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
				'zipCode' => $this->organization['zip'] ?? null,
				'state' => $this->organization['state'] ?? null,
				'country' => $this->organization['country'] ?? null,
			],
		];

		// Sort the files by uploaded date.
		$criteria = Criteria::create()
			->orderBy( [ 'uploaded' => 'DESC' ] );
		$files = $object->getCp()->getFiles()->matching( $criteria );

		// Use the first file's date as the incident date.
		$incidentDateTime = null;
		if ( $files->count() ) {
			$file = $files->first();
			$incidentDateTime = $file->getUploaded()->format( 'c' );
		}

		// Get the Reporter's Email.
		$reporterEmail = [];
		if ( $object->getReporter()->getEmail() ) {
			$reporterEmail = [
				'@verified' => $object->getReporter()->isEmailVerified(),
				'#' => $object->getReporter()->getEmail(),
			];
		}

		// Get the IP Address of all of the uploaded files.
		$capture = $object->getCp()->getFiles()->map( function ( $file ) {
			return [
				'ipAddress' => $file->getIp(),
				'eventName' => 'Upload',
				'dateTime' => $file->getUploaded() ? $file->getUploaded()->format( 'c' ) : null,
			];
		} )->toArray();

		// Add the page urls.
		$urls = $object->getPages()->map( function ( $page ) use ( $object ) {
			return $page->getUrl( $object->getSite() );
		} )->toArray();

		// Add the reported user's screen name.
		$screenName = null;
		$profileUrl = null;
		if ( $object->getInvolved()->count() ) {
			$user = $object->getInvolved()->first();
			$screenName = $user->getUsername();

			if ( $object->getSite() ) {
				$profileUrl = $user->getUrl( $object->getSite() );
			}
		}

		return [
			'incidentSummary' => [
				// @FIXME ASSUMPTION, not even asking yet
				'incidentType' => 'Child Pornography (possession, manufacture, and distribution)',
				'incidentDateTime' => $incidentDateTime,
			],
			'internetDetails' => [
				'webPageIncident' => [
					'url' => $urls,
				],
			],
			'reporter' => [
				'reportingPerson' => [
					'email' => $reporterEmail,
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
				'screenName' => $screenName,
				'profileUrl' => $profileUrl,
				'ipCaptureEvent' => $capture,
				'additionalInfo' => $object->getCp()->getComments()
			],
		];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param mixed $data Data to normalize
	 * @param string|null $format The format being (de-)serialized from or into
	 *
	 * @return bool
	 */
	public function supportsNormalization( $data, $format = null ) {
		return $format === 'ncmec' && $data instanceof Takedown && $data->getCp();
	}
}
