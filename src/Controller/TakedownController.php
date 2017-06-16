<?php

namespace App\Controller;

use App\Entity\Takedown\Takedown;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
	 * Legal Takedown Controller.
	 *
	 * @param SerializerInterface $serializer Serializer.
	 */
	public function __construct(
		SerializerInterface $serializer
	) {
		$this->serializer = $serializer;
	}

	/**
	 * Takedown Index
	 *
	 * @Route("/takedown")
	 * @Method({"GET"})
	 *
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function indexAction( Request $request ) : Response {
		// @TODO Do something with the data!

		return new Response(
			'hello!',
			201
		);
	}

	/**
	 * Takedown
	 *
	 * @Route("/takedown/{takedown}", defaults={"_format" = "json"})
	 * @Method({"GET"})
	 *
	 * @param Takedown $takedown Takedown
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function showAction( Takedown $takedown, Request $request ) : Takedown {
		// @TODO Use a library for this.
		return $takedown;
	}

	/**
	 * Create Legal Takedown
	 *
	 * @Route("/takedown")
	 * @Method({"POST"})
	 *
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function createAction( Request $request ) : Response {
		return new Response(
			'hello!',
			201
		);
	}

}
