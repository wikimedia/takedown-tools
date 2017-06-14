<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use MediaWiki\OAuthClient\Token;
use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AuthController {

	protected $cache;

	protected $client;

	/**
   * @var TokenStorageInterface
   */
  protected $tokenStorage;

	/**
	 * AuthController
	 *
	 * @param CacheInterface $cache PSR Cache Interface
	 * @param Client $client MediWiki OAuth Client.
	 * @param ClientConfig $clientConfig MediWiki Oauth Client Config.
	 * @param TokenStorage $tokenStorage Symfony Token Storage.
	 */
	public function __construct(
		CacheInterface $cache,
		Client $client,
		ClientConfig $clientConfig,
		TokenStorage $tokenStorage
	) {
		$this->cache = $cache;
		$this->client = $client;
		$this->clientConfig = $clientConfig;
		$this->tokenStorage = $tokenStorage;
	}

	/**
	 * Login Action.
	 *
	 * @param Request $request Request object
	 *
	 * @return Response
	 */
	public function loginAction( Request $request ) : Response {
		if ( ! $request->query->has( 'oauth_verifier' ) ) {
			[ $next, $token ] = $this->client->initiate();

			$this->cache->set( 'requestToken.' . $token->key, $token->secret );

			return new RedirectResponse( $next );
		}

		$key = $request->query->get( 'oauth_token', '' );
		if ( ! $this->cache->has( 'requestToken.' . $key ) ) {
			return new RedirectResponse( $request->getPathInfo() );
		}

		$secret = $this->cache->get( 'requestToken.' . $key );
		$token = new Token( $key, $secret );

		$accessToken = $this->client->complete( $token, $request->query->get( 'oauth_verifier' ) );

		// Clear the requestToken from the cache.
		$this->cache->delete( 'requestToken.' . $key );

		$identifyUrl = $this->clientConfig->endpointURL . '/identify';
		$jwt = $this->client->makeOAuthCall( $accessToken, $identifyUrl );

		// @TODO Redirect the user with JavaScript.
		return new Response( '<script type=\"text/javascript\">'
												 . "localStorage.setItem('token', '$jwt');"
												 . '</script>' );
	}

	/**
	 * Refresh User Token.
	 *
	 * @return Response
	 *
	 * @todo Refresh the user token.
	 */
	public function tokenAction() {
		return new JsonResponse( $this->getUser()->getUsername() );
	}

	/**
	* Get a user from the Security Token Storage.
	*
	* @return object|null
	*/
  protected function getUser() {
		$token = $this->tokenStorage->getToken();

		if ( $token === null ) {
			return $token;
		}

		$user = $token->getUser();

		if ( ! is_object( $user ) ) {
				return null;
		}

		return $user;
	 }

}
