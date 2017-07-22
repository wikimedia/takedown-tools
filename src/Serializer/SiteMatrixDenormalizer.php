<?php

namespace App\Serializer;

use App\Entity\Site;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SiteMatrixDenormalizer implements DenormalizerInterface {

	/**
	 * {@inheritdoc}
	 *
	 * @param mixed $data data to restore
	 * @param string $class the expected class to instantiate
	 * @param string $format format the given data was extracted from
	 * @param array $context options available to the denormalizer
	 *
	 * @return object
	 */
	public function denormalize( $data, $class, $format = null, array $context = [] ) {
		$matrix = $data['sitematrix'];
		$sites = [];

		foreach ( $matrix as $key => $value ) {
			if ( $key === 'count' ) {
				continue;
			}

			$data = [];
			if ( array_key_exists( 'site', $value ) ) {
				$data = $value['site'];
			} else {
				$data = $value;
			}

			foreach ( $data as $info ) {
				// @TODO Put the site info somewhere!
				$sites[] = new Site( [
					'id' => $info['dbname'],
					'domain' => parse_url( $info[ 'url' ], PHP_URL_HOST )
				] );
			}
		}

		return $sites;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param mixed $data Data to denormalize from
	 * @param string $type The class to which the data should be denormalized
	 * @param string $format The format being deserialized from
	 *
	 * @return bool
	 */
	public function supportsDenormalization( $data, $type, $format = null ) {
		if ( isset( $data['sitematrix'] ) && substr( $type, -2 ) === '[]' ) {
				$class = substr( $type, 0, -2 );
				if ( $class === Site::class || is_subclass_of( $type, Site::class ) ) {
						return true;
				}
		}

		return false;
	}
}
