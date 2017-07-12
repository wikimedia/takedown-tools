<?php

namespace App\Controller;

use App\Client\MediaWikiClientInterface;
use App\Entity\Takedown\Takedown;
use GeoSocio\EntityAttacher\EntityAttacherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

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
	 * Takedown Controller.
	 *
	 * @param RegistryInterface $doctrine Doctrine.
	 * @param SerializerInterface $serializer Serializer.
	 * @param MediaWikiClientInterface $client MediaWiki Client.
	 * @param EntityAttacherInterface $attacher Entity Attacher.
	 */
	public function __construct(
		RegistryInterface $doctrine,
		SerializerInterface $serializer,
		MediaWikiClientInterface $client,
		EntityAttacherInterface $attacher
	) {
		$this->doctrine = $doctrine;
		$this->serializer = $serializer;
		$this->client = $client;
		$this->attacher = $attacher;
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
		$takedown->setInvolved( $this->client->getUsersByUsernames( $usernames ) );

		// Get the user ids from the API.
		if ( $takedown->getCp() && $takedown->getCp()->getApprover() ) {
			$username = $takedown->getCp()->getApprover()->getUsername();
			$takedown->getCp()->setApprover( $this->client->getUserByUsername( $username ) );
		}

		if ( $takedown->getDmca() && $takedown->getDmca()->getCommonsSend() ) {
			$this->client->postCommons( $takedown->getDmca()->getCommonsText() );
		}

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
	 * Takedown
	 *
	 * @Route("/api/takedown/{takedown}.{_format}", defaults={"_format" = "json"})
	 * @Method({"DELETE"})
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return Response
	 */
	public function deletection( Takedown $takedown ) : string {
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
