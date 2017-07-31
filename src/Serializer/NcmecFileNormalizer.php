<?php

namespace App\Serializer;

use App\Entity\Takedown\ChildProtection\File;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * NCMEC Normalizer
 */
class NcmecFileNormalizer implements NormalizerInterface {

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
		$details = null;

		if ( $object->getExif() ) {
			$details['@type'] = 'EXIF';

			foreach ( $object->getExif() as $name => $value ) {
				if ( !is_scalar( $value ) ) {
					continue;
				}

				$details['#']['nameValuePair'][] = [
					'name' => $name,
					'value' => $value,
				];
			}
		}

		return [
			'reportId' => $object->getCp()->getNcmecId(),
			'fileId' => $object->getNcmecId(),
			'fileName' => $object->getName(),
			'ipCaptureEvent' => [
				'ipAddress' => $object->getIp(),
				'eventName' => 'Upload',
				'dateTime' => $object->getUploaded() ? $object->getUploaded()->format( 'c' ) : null,
			],
			'details' => $details,
		];
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
		return $format === 'ncmec' && $data instanceof File;
	}
}
