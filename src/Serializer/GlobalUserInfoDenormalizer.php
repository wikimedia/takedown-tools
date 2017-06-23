<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class GlobalUserInfoDenormalizer implements DenormalizerInterface {

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
		$info = $data['query']['globaluserinfo'];

		return new User( [
			'id' => !empty( $info['id'] ) ? $info['id'] : null,
			'username' => !empty( $info['name'] ) ? $info['name'] : null
		] );
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
		if ( isset( $data['query'] ) && isset( $data['query']['globaluserinfo'] ) ) {
				if ( $type === User::class || is_subclass_of( $type, User::class ) ) {
						return true;
				}
		}
		return false;
	}
}
