<?php

namespace App\Client;

use App\Entity\Site;
use App\Entity\User;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
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
	 * @var string
	 */
	protected $environment;

	/**
	 * MediaWiki
	 *
	 * @param ClientInterface $client Configured Guzzle Client.
	 * @param SerializerInterface $serializer Syfmony Serializer.
	 * @param string $environment Kernel Environment.
	 */
	public function __construct(
		ClientInterface $client,
		SerializerInterface $serializer,
		string $environment
	) {
		$this->client = $client;
		$this->serializer = $serializer;
		$this->environment = $environment;
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
	 * Post notice to Commons.
	 *
	 * @param string $text Text to Post.
	 *
	 * @return array
	 */
	public function postCommons( string $text ) :? array {
		$url = 'https://test2.wikipedia.org/w/api.php';
		$title = 'Office_actions/DMCA_notices';

		if ( $this->environment === 'prod' ) {
			$url = 'https://commons.wikimedia.org/w/api.php';
			$title = 'Commons:Office_actions/DMCA_notices';
		}

		$request = new Request( 'POST', $url );

		$response = $this->client->send( $request, [
			'query' => [
				'action' => 'edit',
				'format' => 'json',
			],
			'form_params' => [
				'title' => $title,
				'summary' => 'new takedown',
				'appendtext' => $text,
				'recreate' => true,
				'token' => $this->getToken( $url ),
			],
			'auth' => 'oauth',
		] );

		$data = json_decode( $response->getBody(), true );

		if ( array_key_exists( 'error', $data ) ) {
			throw new BadResponseException( $data['error']['info'], $request, $response );
		}

		return json_decode( $response->getBody(), true );
	}

	/**
	 * Get Token.
	 *
	 * @param string $uri URI
	 *
	 * @return string
	 */
	protected function getToken( $uri = '' ) :? string {
		$response = $this->client->get( $uri, [
			'query' => [
				'action' => 'tokens',
				'format' => 'json',
			],
			'auth' => 'oauth',
		] );

		$data = json_decode( $response->getBody(), true );

		return !empty( $data['tokens']['edittoken'] ) ? $data['tokens']['edittoken'] : null;
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
