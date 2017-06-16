<?php

namespace App\Controller\Takedown;

use App\Entity\LegalTakedown;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class TakedownController {

	/**
	 * @var SerializerInterface
	 */
	protected $serializer;

	/**
	 * Legal Takedown Controller.
	 *
	 * @param Serializer $serializer Serializer.
	 */
	public function __construct(
		SerializerInterface $serializer
	) {
		$this->serializer = $serializer;
	}

	/**
	 * Create Legal Takedown
	 *
	 * @param Request $request Request
	 *
	 * @return Response
	 */
	public function createAction( Request $request ) : Response {
		$takedown = $this->serializer->deserialize(
			$request->getContent(),
			LegalTakedown::class,
			$request->getRequestFormat()
		);

		 $auth = new \Symfony\Component\Security\Core\Authorization\AuthorizationChecker  ()
		// @TODO Do something with the data!

		return new Response(
			$this->serializer->serialize( $takedown, $request->getRequestFormat() ),
			201
		);
	}

}
