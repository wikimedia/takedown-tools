<?php

namespace App\Client;

use App\Entity\Site;
use App\Entity\User;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;
use function GuzzleHttp\Promise\settle;

class MediaWikiClient implements MediaWikiClientInterface {

	/**
	 * @var ClientInterface
	 */
	protected $client;

	/**
	 * @var SerializerInterface
	 */
	protected $serializer;

	/**
	 * MediaWiki
	 *
	 * @param ClientInterface $client Configured Guzzle Client.
	 * @param SerializerInterface $serializer Syfmony Serializer.
	 */
	public function __construct( ClientInterface $client, SerializerInterface $serializer ) {
		$this->client = $client;
		$this->serializer = $serializer;
	}

	/**
	 * Get User by Username
	 *
	 * @param string[] $usernames Users to retrieve.
	 *
	 * @return User[]
	 */
	public function getUsersByUsernames( array $usernames ) : array {
		 return $this->getUsersAsync( $usernames )->wait();
	}

	/**
	 * Get User by Username
	 *
	 * @param string $username Users to retrieve.
	 *
	 * @return User
	 */
	public function getUserByUsername( string $username ) : User {
		 return $this->getUserAsync( $username )->wait();
	}

	/**
	 * Get Site by Id
	 *
	 * @param string $id Site to retrieve
	 *
	 * @return Site
	 */
	public function getSiteById( string $id ) : Site {
		return $this->getSitesAsync()->then( function ( $sites ) use ( $id ) {
			$item = reset( $sites );
			while ( $item !== false ) {
					if ( $item->getId() === $id ) {
							return $item;
					};

					$item = next( $sites );
			}

			return null;
		} )->wait();
	}

	/**
	 * Get Users by Usernames
	 *
	 * @param string $usernames Users to retrieve.
	 *
	 * @return PromiseInterface
	 */
	protected function getUsersAsync( array $usernames ) : PromiseInterface {
		$promises = array_map( function( $username ) {
			return $this->getUserAsync( $username );
		}, $usernames );

		return settle( $promises )->then( function( $results ) {
			$results = array_filter( $results, function ( $result ) {
				return $result['state'] === PromiseInterface::FULFILLED;
			} );

			return array_map( function( $result ) {
				return $result['value'];
			}, $results );
		} );
	}

	/**
	 * Get User by Username
	 *
	 * @param string $username Users to retrieve.
	 *
	 * @return PromiseInterface
	 */
	protected function getUserAsync( string $username ) : PromiseInterface {
		return $this->client->getAsync( '', [
			'query' => [
				'action' => 'query',
				'format' => 'json',
				'meta' => 'globaluserinfo',
				'guiuser' => $username,
			],
		] )->then( function( ResponseInterface $response ) {
			return $this->serializer->deserialize( (string)$response->getBody(), User::class, 'json' );
		}, function ( RequestException $e ) {
			return null;
		} );
	}

	/**
	 * Get All Sites.
	 *
	 * @return PromiseInterface
	 */
	protected function getSitesAsync() : PromiseInterface {
		return $this->client->getAsync( '', [
			'query' => [
				'action' => 'sitematrix',
				'format' => 'json',
			],
		] )->then( function( ResponseInterface $response ) {
			return $this->serializer->deserialize(
				(string)$response->getBody(),
				Site::class . '[]',
				'json'
			);
		}, function ( RequestException $e ) {
			return [];
		} );
	}
}
