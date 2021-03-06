<?php

namespace App\Controller;

use App\Client\NcmecClientInterface;
use App\Entity\User;
use App\Client\MediaWikiClientInterface;
use App\Entity\Takedown\Takedown;
use App\Entity\Takedown\Dmca\Post;
use App\Entity\Takedown\ChildProtection\File;
use App\Util\TakedownUtilInterface;
use GuzzleHttp\Exception\RequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Route(service="app.controller_takedown")
 */
class TakedownController {

	/**
	 * @var RegistryInterface
	 */
	protected $doctrine;

	/**
	 * @var MediaWikiClientInterface
	 */
	protected $client;

	/**
	 * @var NcmecClientInterface
	 */
	protected $ncmecClient;

	/**
	 * @var TakedownUtilInterface
	 */
	protected $takedownUtil;

	/**
	 * Takedown Controller.
	 *
	 * @param RegistryInterface $doctrine Doctrine.
	 * @param MediaWikiClientInterface $client MediaWiki Client.
	 * @param NcmecClientInterface $ncmecClient NCMEC Client.
	 * @param TakedownUtilInterface $takedownUtil Takedown Utility.
	 */
	public function __construct(
		RegistryInterface $doctrine,
		MediaWikiClientInterface $client,
		NcmecClientInterface $ncmecClient,
		TakedownUtilInterface $takedownUtil
	) {
		$this->doctrine = $doctrine;
		$this->client = $client;
		$this->ncmecClient = $ncmecClient;
		$this->takedownUtil = $takedownUtil;
	}

	/**
	 * Takedown Index
	 *
	 * @Route("/api/takedown.{_format}", defaults={"_format" = "json"})
	 * @Method({"GET"})
	 * @Groups({"api"})
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
	 * @ParamConverter("takedown", class="App\Entity\Takedown\Takedown")
	 * @Groups({"api"})
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
	 * @Groups({"api"})
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return Response
	 */
	public function createAction( Takedown $takedown ) {
		return $this->takedownUtil->create( $takedown )->wait();
	}

	/**
	 * Create Legal Takedown
	 *
	 * @Route("/api/takedown/{takedown}/commons", defaults={"_format" = "json"})
	 * @ParamConverter("takedown", class="App\Entity\Takedown\Takedown")
	 * @Method({"POST"})
	 * @Groups({"api"})
	 *
	 * @param Takedown $takedown Takedown
	 * @param Post $post Post
	 *
	 * @return Response
	 */
	public function createCommonsPostAction( Takedown $takedown, Post $post ) {
		$em = $this->doctrine->getEntityManager();

		try {
			$id = $this->client->postCommons( $post )->wait();
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

		$takedown->getDmca()->setCommonsId( $id );

		$em->flush();

		return $takedown;
	}

	/**
	 * Create Legal Takedown
	 *
	 * @Route("/api/takedown/{takedown}/commons-village-pump", defaults={"_format" = "json"})
	 * @ParamConverter("takedown", class="App\Entity\Takedown\Takedown")
	 * @Method({"POST"})
	 * @Groups({"api"})
	 *
	 * @param Takedown $takedown Takedown
	 * @param Post $post Post
	 *
	 * @return Response
	 */
	public function createCommonsVillagePumpPostAction( Takedown $takedown, Post $post ) {
		$em = $this->doctrine->getEntityManager();

		try {
			$id = $this->client->postCommonsVillagePump( $post )->wait();
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

		$takedown->getDmca()->setCommonsVillagePumpId( $id );

		$em->flush();

		return $takedown;
	}

	/**
	 * Create Legal Takedown
	 *
	 * @Route("/api/takedown/{takedown}/user-notice/{user}", defaults={"_format" = "json"})
	 * @ParamConverter("takedown", class="App\Entity\Takedown\Takedown")
	 * @ParamConverter("user", class="App\Entity\User")
	 * @Method({"POST"})
	 * @Groups({"api"})
	 *
	 * @param Takedown $takedown Takedown
	 * @param User $user User
	 * @param Post $post Post
	 *
	 * @return Response
	 */
	public function createUserNoticeAction(
		Takedown $takedown,
		User $user,
		Post $post
	) {
		$em = $this->doctrine->getEntityManager();

		if ( !$takedown->getSite() ) {
			throw new BadRequestHttpException( 'Takedown is missing Site' );
		}

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
	 * @ParamConverter("takedown", class="App\Entity\Takedown\Takedown")
	 * @Method({"DELETE"})
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return string
	 */
	public function deleteAction( Takedown $takedown ) : string {
		$this->takedownUtil->delete( $takedown )->wait();
		return '';
	}

	/**
	 * Send NCMEC File
	 *
	 * @Route("/api/takedown/{takedown}/ncmec/file/{file}", defaults={"_format" = "json"})
	 * @ParamConverter("takedown", class="App\Entity\Takedown\Takedown")
	 * @ParamConverter("file", class="App\Entity\Takedown\ChildProtection\File")
	 * @Method({"POST"})
	 * @Groups({"api"})
	 *
	 * @param Takedown $takedown Takedown
	 * @param File $file File
	 * @param Request $request Request
	 *
	 * @return array
	 */
	public function sendNcmecFileAction(
		Takedown $takedown,
		File $file,
		Request $request
	) : Takedown {
		if ( !$takedown->getCp() ) {
			throw new BadRequestHttpException( 'Takedown is missing Child Protection' );
		}

		if ( !$takedown->getCp()->getNcmecId() ) {
			throw new BadRequestHttpException( 'Takedown is missing NCMEC ID' );
		}

		$em = $this->doctrine->getEntityManager();

		$file = $this->ncmecClient->sendFile( $takedown, $file, $request->getContent( true ) )->wait();
		$file = $em->merge( $file );

		$em->flush();

		$em->refresh( $takedown );

		return $takedown;
	}

	/**
	 * Send NCMEC File
	 *
	 * @Route("/api/takedown/{takedown}/ncmec/finish", defaults={"_format" = "json"})
	 * @ParamConverter("takedown", class="App\Entity\Takedown\Takedown")
	 * @Method({"POST"})
	 * @Groups({"api"})
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return array
	 */
	public function finishNcmecReportAction( Takedown $takedown ) : Takedown {
		if ( !$takedown->getCp() ) {
			throw new BadRequestHttpException( 'Takedown is missing Child Protection' );
		}

		if ( !$takedown->getCp()->getNcmecId() ) {
			throw new BadRequestHttpException( 'Takedown is missing NCMEC ID' );
		}

		// Finish the report.
		$this->ncmecClient->finishReport( $takedown )->wait();

		return $takedown;
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
