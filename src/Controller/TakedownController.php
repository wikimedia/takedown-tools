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

		$takedown = $this->attacher->attach( $takedown );

		$em->persist( $takedown );
		$em->flush();

		return $this->showAction( $takedown );
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
