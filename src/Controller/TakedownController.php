<?php

namespace App\Controller;

use App\Client\LumenClientInterface;
use App\Entity\User;
use App\Client\MediaWikiClientInterface;
use App\Entity\Takedown\Takedown;
use App\Entity\Takedown\Dmca\Post;
use GeoSocio\EntityAttacher\EntityAttacherInterface;
use GuzzleHttp\Exception\RequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use function GuzzleHttp\Promise\settle;

/**
 * @Route(service="app.controller_takedown")
 */
class TakedownController {

	/**
	 * @var RegistryInterface
	 */
	protected $doctrine;

	/**
	 * @var SerializerInterface
	 */
	protected $serializer;

	/**
	 * @var MediaWikiClientInterface
	 */
	protected $client;

	/**
	 * @var EntityAttacherInterface
	 */
	protected $attacher;

	/**
	 * @var LumenClientInterface
	 */
	protected $lumenClient;

	/**
	 * Takedown Controller.
	 *
	 * @param RegistryInterface $doctrine Doctrine.
	 * @param SerializerInterface $serializer Serializer.
	 * @param MediaWikiClientInterface $client MediaWiki Client.
	 * @param EntityAttacherInterface $attacher Entity Attacher.
	 * @param LumenClientInterface $lumenClient Lumen Client.
	 */
	public function __construct(
		RegistryInterface $doctrine,
		SerializerInterface $serializer,
		MediaWikiClientInterface $client,
		EntityAttacherInterface $attacher,
		LumenClientInterface $lumenClient
	) {
		$this->doctrine = $doctrine;
		$this->serializer = $serializer;
		$this->client = $client;
		$this->attacher = $attacher;
		$this->lumenClient = $lumenClient;
	}

	/**
	 * Takedown Index
	 *
	 * @Route("/api/takedown.{_format}", defaults={"_format" = "json"})
	 * @Method({"GET"})
	 *
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function indexAction( Request $request ) : array {
		$repository = $this->doctrine->getRepository( Takedown::class );

		return $repository->findBy(
			[],
			[ 'created' => 'DESC' ],
			$this->getLimit( $request ),
			$this->getOffset( $request )
		);
	}

	/**
	 * Takedown
	 *
	 * @Route("/api/takedown/{takedown}.{_format}", defaults={"_format" = "json"})
	 * @Method({"GET"})
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return Response
	 */
	public function showAction( Takedown $takedown ) : Takedown {
		return $takedown;
	}

	/**
	 * Create Legal Takedown
	 *
	 * @Route("/api/takedown", defaults={"_format" = "json"})
	 * @Method({"POST"})
	 *
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function createAction( Request $request ) : Takedown {
		$em = $this->doctrine->getEntityManager();
		$promises = [];

		$takedown = $this->serializer->deserialize(
			$request->getContent(),
			Takedown::class,
			$request->getRequestFormat(),
			[
				'groups' => [ 'api' ]
			]
		);

		// Get the user ids from the API.
		$usernames = $takedown->getInvolvedNames();

		if ( $usernames ) {
			$promises[] = $this->client->getUsers( $usernames )
				->then( function( $users ) use ( $takedown ) {
					$takedown->setInvolved( $users );
				} );
		}

		// Get the user ids from the API.
		if ( $takedown->getCp() && $takedown->getCp()->getApprover() ) {
			$username = $takedown->getCp()->getApprover()->getUsername();
			$promises[] = $this->client->getUser( $username )
				->then( function( $user ) use ( $takedown ) {
					$takedown->getCp()->setApprover( $user );
				} );
		}

		// Send to Lumen.
		if ( $takedown->getDmca() && $takedown->getDmca()->getLumenSend() ) {
			$promises[] = $this->lumenClient->postNotice( $takedown )->then( function( $response ) {
				dump( $response );
				exit;
				// @TODO Save the notice id in the database!
			} );
		}

		// Settle the promises.
		// The requests are not executed unless we explicitly wait since we are not
		// in an event loop.
		settle( $promises )->wait();

		// Attach the takedown to existing entities.
		$takedown = $this->attacher->attach( $takedown );

		// Remove the related entities.
		// @link https://github.com/doctrine/doctrine2/issues/6531
		$dmca = $takedown->getDmca();
		$takedown->setDmca();
		$cp = $takedown->getCp();
		$takedown->setCp();

		$em->persist( $takedown );
		$em->flush();

		// Add the related entities back and persist them.
		// @link https://github.com/doctrine/doctrine2/issues/6531
		$takedown->setDmca( $dmca );
		$takedown->setCp( $cp );

		if ( $takedown->getDmca() ) {
			$em->persist( $takedown->getDmca() );
		}
		if ( $takedown->getCP() ) {
			$em->persist( $takedown->getCp() );
		}
		$em->flush();

		return $this->showAction( $takedown );
	}

	/**
	 * Create Legal Takedown
	 *
	 * @Route("/api/takedown/{takedown}/commons", defaults={"_format" = "json"})
	 * @Method({"POST"})
	 *
	 * @param Takedown $takedown Takedown
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function createCommonsPostAction( Takedown $takedown, Request $request ) {
		$em = $this->doctrine->getEntityManager();

		$post = $this->serializer->deserialize(
			$request->getContent(),
			Post::class,
			$request->getRequestFormat(),
			[
				'groups' => [ 'api' ]
			]
		);

		try {
			$this->client->postCommons( $post )->wait();
		} catch ( RequestException $e ) {
			if ( $e->getResponse()->getStatusCode() !== 200 ) {
				throw $e;
			}

			$data = $e->getHandlerContext();

			if ( !array_key_exists( 'captcha', $data ) ) {
				throw $e;
			}

			return new JsonResponse( $data, 409 );
		}

		$takedown->getDmca()->setCommonsSend( true );

		$em->flush();

		return $takedown;
	}

	/**
	 * Create Legal Takedown
	 *
	 * @Route("/api/takedown/{takedown}/commons-village-pump", defaults={"_format" = "json"})
	 * @Method({"POST"})
	 *
	 * @param Takedown $takedown Takedown
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function createCommonsVillagePumpPostAction(
		Takedown $takedown,
		Request $request
	) {
		$em = $this->doctrine->getEntityManager();

		$post = $this->serializer->deserialize(
			$request->getContent(),
			Post::class,
			$request->getRequestFormat(),
			[
				'groups' => [ 'api' ]
			]
		);

		try {
			$this->client->postCommonsVillagePump( $post )->wait();
		} catch ( RequestException $e ) {
			if ( $e->getResponse()->getStatusCode() !== 200 ) {
				throw $e;
			}

			$data = $e->getHandlerContext();

			if ( !array_key_exists( 'captcha', $data ) ) {
				throw $e;
			}

			return new JsonResponse( $data, 409 );
		}

		$takedown->getDmca()->setCommonsVillagePumpSend( true );

		$em->flush();

		return $takedown;
	}

	/**
	 * Create Legal Takedown
	 *
	 * @Route("/api/takedown/{takedown}/user-notice/{user}", defaults={"_format" = "json"})
	 * @Method({"POST"})
	 *
	 * @param Takedown $takedown Takedown
	 * @param User $user User
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function createUserNoticeAction(
		Takedown $takedown,
		User $user,
		Request $request
	) {
		$em = $this->doctrine->getEntityManager();

		$post = $this->serializer->deserialize(
			$request->getContent(),
			Post::class,
			$request->getRequestFormat(),
			[
				'groups' => [ 'api' ]
			]
		);

		// Ensure the user is in the invovled users and *not* in the existing
		// list of notices that have been sent.
		$exists = $takedown->getDmca()->getUserNotices()->exists( function ( $key, $item ) use ( $user ) {
			return $item->getId() === $user->getId();
		} );

		if ( $exists ) {
			throw new BadRequestHttpException( 'User Notice Already Sent' );
		}

		$exists = $takedown->getInvolved()->exists( function ( $key, $item ) use ( $user ) {
			return $item->getId() === $user->getId();
		} );
		if ( !$exists ) {
			throw new BadRequestHttpException( 'User is not an invovled user' );
		}

		try {
			$this->client->getSite( $takedown->getSite()->getId() )
				->then( function ( $site ) use ( $user, $post ) {
					return $this->client->postUserTalk( $site, $user, $post );
				} )->wait();
		} catch ( RequestException $e ) {
			if ( $e->getResponse()->getStatusCode() !== 200 ) {
				throw $e;
			}

			$data = $e->getHandlerContext();

			if ( !array_key_exists( 'captcha', $data ) ) {
				throw $e;
			}

			return new JsonResponse( $data, 409 );
		}

		$takedown->getDmca()->addUserNotice( $user );

		$em->flush();

		return $takedown;
	}

	/**
	 * Takedown
	 *
	 * @Route("/api/takedown/{takedown}.{_format}", defaults={"_format" = "json"})
	 * @Method({"DELETE"})
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return Response
	 */
	public function deleteAction( Takedown $takedown ) : string {
		$em = $this->doctrine->getEntityManager();

		$em->remove( $takedown );
		$em->flush();

		return '';
	}

	/**
	 * Gets the offset from the Request.
	 *
	 * @param Request $request Request
	 *
	 * @return int
	 */
	public function getOffset( Request $request ) : int {
			$limit = $this->getLimit( $request );
			$page = $request->query->getInt( 'page', 1 );
			$offset = ( $page * $limit ) - $limit;

			// Offset cannot be negative.
			if ( $offset < 0 ) {
					$offset = 0;
			}

			return $offset;
	}

	/**
	 * Gets the limit from the Request.
	 *
	 * @param Request $request Request
	 *
	 * @return int
	 */
	public function getLimit( Request $request ) : int {
			return $request->query->getInt( 'limit', 5 );
	}

}
