<?php

namespace App\Encoder;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use JWT\Authentication\JWT;

class TokenEncoder implements JWTEncoderInterface {

			/**
			 * @var string
			 */
			protected $secret;

			/**
			 * MediaWikiJWTEncoder
			 *
			 * @param string $secret The secret to encode/decode the JWT
			 */
			public function __construct( string $secret ) {
					$this->secret = $secret;
			}

			/**
			 * {@inheritdoc}
			 *
			 * @param array $data Data to encode
			 *
			 * @return string
			 */
			public function encode( array $data ) {
					return JWT::encode( $data, $this->secret );
			}

			/**
			 * {@inheritdoc}
			 *
			 * @param string $token The JWT token string.
			 *
			 * @return array
			 */
			public function decode( $token ) {
					try {
							return (array)JWT::decode( $token, $this->secret, 'HS256' );
					} catch ( \Exception $e ) {
							throw new JWTDecodeFailureException( '', $e->getMessage(), $e );
					}
			}
}
