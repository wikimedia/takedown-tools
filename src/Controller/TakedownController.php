<?php

namespace App\Controller;

use App\Entity\Takedown\Takedown;
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
	 * @var SerializerInterface
	 */
	protected $serializer;

	/**
	 * @var RegistryInterface
	 */
	protected $doctrine;

	/**
	 * Takedown Controller.
	 *
	 * @param SerializerInterface $serializer Serializer.
	 * @param RegistryInterface $doctrine Doctrine.
	 */
	public function __construct(
		SerializerInterface $serializer,
		RegistryInterface $doctrine
	) {
		$this->serializer = $serializer;
		$this->doctrine = $doctrine;
	}

	/**
	 * Takedown Index
	 *
	 * @Route("/takedown", defaults={"_format" = "json"})
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
			[
				'created' => 'DESC',
			],
			$this->getLimit( $request ),
			$this->getOffset( $request )
		);
	}

	/**
	 * Takedown
	 *
	 * @Route("/takedown/{takedown}", defaults={"_format" = "json"})
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
	 * @Route("/takedown", defaults={"_format" = "json"})
	 * @Method({"POST"})
	 *
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function createAction( Request $request ) : Response {
		// @TODO Implement Method.
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
