<?php

namespace App\Controller;

use App\Client\MediaWikiClientInterface;
use App\Entity\User;
use GuzzleHttp\ClientInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use MediaWiki\OAuthClient\Token;
use MediaWiki\OAuthClient\Client;
use Psr\SimpleCache\CacheInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route(service="app.controller_auth")
 */
class AuthController {

	/**
	 * @var CacheInterface
	 */
	protected $cache;

	/**
	 * @var MediaWikiClientInterface
	 */
	protected $client;

	/**
	 * @var Client
	 */
	protected $oauthClient;

	/**
	 * @var TokenStorageInterface
	 */
	protected $tokenStorage;

	/**
	 * @var RegistryInterface
	 */
	protected $doctrine;

	/**
	 * AuthController
	 *
	 * @param CacheInterface $cache PSR Cache Interface
	 * @param MediaWikiClientInterface $client MediWiki Client.
	 * @param Client $oauthClient MediWiki OAuth Client.
	 * @param TokenStorageInterface $tokenStorage Symfony Token Storage.
	 * @param JWTTokenManagerInterface $jwtManager JWT Manager
	 * @param RegistryInterface $doctrine Doctrine
	 */
	public function __construct(
		CacheInterface $cache,
		MediaWikiClientInterface $client,
		Client $oauthClient,
		TokenStorageInterface $tokenStorage,
		JWTTokenManagerInterface $jwtManager,
		RegistryInterface $doctrine
	) {
		$this->cache = $cache;
		$this->client = $client;
		$this->oauthClient = $oauthClient;
		$this->tokenStorage = $tokenStorage;
		$this->jwtManager = $jwtManager;
		$this->doctrine = $doctrine;
	}

	/**
	 * Login Action.
	 *
	 * @Route("/login", defaults={"_format" = "html"})
	 * @Method({"GET"})
	 *
	 * @param Request $request Request object
	 *
	 * @return Response
	 */
	public function loginAction( Request $request ) : Response {
		// If there is no 'oauth_verifier' the user needs to login and give
		// access.
		if ( ! $request->query->has( 'oauth_verifier' ) ) {
			[ $next, $token ] = $this->oauthClient->initiate();

			$this->cache->set( 'requestToken.' . $token->key, $token->secret );

			return new RedirectResponse( $next );
		}

		$key = $request->query->get( 'oauth_token', '' );

		// If the request token is missing from the cache, we need it.
		if ( ! $this->cache->has( 'requestToken.' . $key ) ) {
			return new RedirectResponse( $request->getPathInfo() );
		}

		$secret = $this->cache->get( 'requestToken.' . $key );
		$accessToken = $this->oauthClient->complete(
			new Token( $key, $secret ),
			$request->query->get( 'oauth_verifier' )
		);

		// Clear the requestToken from the cache.
		$this->cache->delete( 'requestToken.' . $key );

		// Get the user's identiy.
		$identiy = $this->oauthClient->identify( $accessToken );

		// Load the user from the API.
		$user = $this->client->getUser( $identiy->username )->wait();
		$user->setRoles( $this->getRolesFromGroups( $identiy->groups ) );

		// If the user is not already in the database, save it.
		$existing = null;
		$em = $this->doctrine->getEntityManager();
		$existing = $em->find( User::class, $user->getId() );

		if ( !$existing ) {
			$em->persist( $user );
			$em->flush();
		}

		$user->setToken( $accessToken->key );
		$user->setSecret( $accessToken->secret );

		// We cannot use the JWT's returned from MediaWiki becaused they are
		// short-lived tokens.
		$jwt = $this->jwtManager->create( $user );

		return new RedirectResponse( '/?token=' . $jwt );
	}

	/**
	 * Refresh User Token.
	 *
	 * @Route("/api/token.{_format}", defaults={"_format" = "json"})
	 * @Method({"GET"})
	 *
	 * @return Response
	 */
	public function tokenAction() : array {
			return [
				'token' => $this->jwtManager->create( $this->getUser() )
			];
	}

 /**
	* Get roles from groups.
	*
	* @param array $groups Groups.
	*
	* @return array
	*/
	protected function getRolesFromGroups( array $groups ) : array {
		$groups = array_filter( $groups, function( $group ) {
			return $group !== "*";
		} );

		$roles = array_map( function( $group ) {
			return 'ROLE_' . strtoupper( $group );
		}, $groups );

		return array_values( $roles );
	}

 /**
	* Get a user from the Security Token Storage.
	*
	* @return User
	*/
	protected function getUser() :? User {
		$token = $this->tokenStorage->getToken();

		if ( $token === null ) {
			return $token;
		}

		$user = $token->getUser();

		if ( ! $user instanceof User ) {
				return null;
		}

		return $user;
	}

}
