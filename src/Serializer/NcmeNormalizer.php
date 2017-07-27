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
	 * {@inheritdoc}
	 *
	 * @param object $object Object to normalize
	 * @param string $format Format the normalization result will be encoded as
	 * @param array $context Context options for the normalizer
	 *
	 * @return array
	 */
	public function normalize( $object, $format = null, array $context = [] ) {
		$data = [
			'incidentSummary' => [
				// @FIXME ASSUMPTION, not even asking yet
				'incidentType' => 'Child Pornography (possession, manufacture, and distribution)',
			],
			'internetDetails' => [],
		];

		$criteria = Criteria::create()
			->orderBy( [ 'uploaded' => 'DESC' ] );
		$files = $object->getCp()->getFiles()->matching( $citeria );

		if ( $files->count() ) {
			$file = $files->first();
			$data['incidentSummary']['incidentDateTime'] = $file->getUploaded()->format( 'c' );
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
		return $format === 'ncme' && $data instanceof Takedown && $data->getCp();
	}
}
