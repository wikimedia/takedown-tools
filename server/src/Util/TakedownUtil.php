<?php

namespace App\Util;

use App\Client\NcmecClientInterface;
use App\Client\LumenClientInterface;
use App\Client\MediaWikiClientInterface;
use App\Entity\User;
use App\Entity\Takedown\Takedown;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use GeoSocio\EntityAttacher\EntityAttacherInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Takedown Utility.
 */
class TakedownUtil implements TakedownUtilInterface {

	/**
	 * @var RegistryInterface
	 */
	protected $doctrine;

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
	 * @var NcmecClientInterface
	 */
	protected $ncmecClient;

	/**
	 * @var TokenStorageInterface
	 */
	protected $tokenStorage;

	/**
	 * Takedown Controller.
	 *
	 * @param RegistryInterface $doctrine Doctrine.
	 * @param MediaWikiClientInterface $client MediaWiki Client.
	 * @param EntityAttacherInterface $attacher Entity Attacher.
	 * @param LumenClientInterface $lumenClient Lumen Client.
	 * @param NcmecClientInterface $ncmecClient NCMEC Client.
	 * @param TokenStorageInterface $tokenStorage Token Storage.
	 */
	public function __construct(
		RegistryInterface $doctrine,
		MediaWikiClientInterface $client,
		EntityAttacherInterface $attacher,
		LumenClientInterface $lumenClient,
		NcmecClientInterface $ncmecClient,
		TokenStorageInterface $tokenStorage
	) {
		$this->doctrine = $doctrine;
		$this->client = $client;
		$this->attacher = $attacher;
		$this->lumenClient = $lumenClient;
		$this->ncmecClient = $ncmecClient;
		$this->tokenStorage = $tokenStorage;
	}

	/**
	 * Creates a Takedown
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function create( Takedown $takedown ) : PromiseInterface {
		$promises = [];

		if ( $takedown->getReporter() ) {
			// If the reporter is the same as the current user, set the current user
			// object as the report since it has more data.
			if ( $this->getUser() && $takedown->getReporter()->getId() === $this->getUser()->getId() ) {
				$takedown->setReporter( $this->getUser() );
			} elseif ( $takedown->getReporter()->getUsername() ) {
				$promises[] = $this->client->getUser( $takedown->getReporter()->getUsername() )
					->then( function ( $user ) use ( $takedown ) {
						$takedown->setReporter( $user );
					} );
			}
		}

		// Get the user ids from the API.
		$usernames = $takedown->getInvolvedNames();

		if ( $usernames ) {
			$promises[] = $this->client->getUsers( $usernames )
				->then( function ( $users ) use ( $takedown ) {
					$takedown->setInvolved( $users );
				} );
		}

		// Get the user ids from the API.
		if ( $takedown->getCp() && $takedown->getCp()->getApprover() ) {
			$username = $takedown->getCp()->getApprover()->getUsername();
			$promises[] = $this->client->getUser( $username )
				->then( function ( $user ) use ( $takedown ) {
					$takedown->getCp()->setApprover( $user );
					return $takedown;
				} );
		}

		// Send to Lumen.
		if ( $takedown->getDmca() && $takedown->getDmca()->getLumenSend() ) {
			$promises[] = $this->lumenClient->createNotice( $takedown )
				->then( function ( $noticeId ) use ( $takedown ) {
					$takedown->getDmca()->setLumenId( $noticeId );
					return $takedown;
				} );
		}

		// Send to NCME
		if ( $takedown->getCp()
			&& !$takedown->getCp()->getNcmecId() && $takedown->getCp()->isApproved()
		) {
			$promises[] = $this->ncmecClient->createReport( $takedown )
				->then( function ( $reportId ) use ( $takedown ) {
					$takedown->getCp()->setNcmecId( $reportId );
					return $takedown;
				} );
		}

		// Settle the promises.
		// The requests are not executed unless we explicitly wait since we are not
		// in an event loop.
		return \GuzzleHttp\Promise\all( $promises )->then( function () use ( $takedown ) {
			$em = $this->doctrine->getEntityManager();

			// Attach the takedown to existing entities.
			$takedown = $this->attacher->attach( $takedown );

			// Remove the related entities.
			// @link https://github.com/doctrine/doctrine2/issues/6531
			$dmca = $takedown->getDmca();
			$takedown->setDmca();
			$cp = $takedown->getCp();
			$takedown->setCp();

			// If an id was explicitly set, then avoid autogeneration.
			if ( $takedown->getId() ) {
				$metadata = $em->getClassMetadata( Takedown::class );
				$metadata->setIdGenerator( new AssignedGenerator() );
				$metadata->setIdGeneratorType( ClassMetadata::GENERATOR_TYPE_NONE );
			}

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

			return $takedown;
		} );
	}

	/**
	 * Deletes a Takedown
	 *
	 * @param Takedown $takedown Takedown
	 *
	 * @return PromiseInterface
	 */
	public function delete( Takedown $takedown ) : PromiseInterface {
		$promises = [];

		// Retract the report before deleting the takedown.
		if ( $takedown->getCp() && $takedown->getCp()->getNcmecId() ) {
			$promises[] = $this->ncmecClient->retractReport( $takedown )
				->then( function ( $result ) use ( $takedown ) {
					$takedown->getCp()->setNcmecId( null );
					return $takedown;
				} )->wait();
		}

		return \GuzzleHttp\Promise\all( $promises )->then( function () use ( $takedown ) {
			$em = $this->doctrine->getEntityManager();

			$em->remove( $takedown );
			$em->flush();
		} );
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
