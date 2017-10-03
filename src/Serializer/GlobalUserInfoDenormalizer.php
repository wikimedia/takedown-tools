<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Global User Info Denormalizer.
 */
class GlobalUserInfoDenormalizer implements DenormalizerInterface {

	/**
	 * {@inheritdoc}
	 *
	 * @param mixed $data data to restore
	 * @param mixed $class the expected class to instantiate
	 * @param string|null $format format the given data was extracted from
	 * @param array $context options available to the denormalizer
	 *
	 * @return object
	 */
	public function denormalize( $data, $class, $format = null, array $context = [] ) {
		$info = $data['query']['globaluserinfo'];

		return new User( [
			'id' => $info['id'] ?? null,
			'username' => $info['name'] ?? null,
			'roles' => User::getRolesFromGroups( $info['groups'] ?? [] )
		] );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param mixed $data Data to denormalize from
	 * @param mixed $type The class to which the data should be denormalized
	 * @param string|null $format The format being deserialized from
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
