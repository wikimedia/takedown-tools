<?php

namespace App\DataFixtures\ORM;

use App\Entity\Metadata;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMetadata implements FixtureInterface {

	// @TODO Add the title?s
	const METADATA = [
		'taken-down-user-warned',
		'taken-down-infringing',
		'taken-down-apparent',
		'taken-down-dmca',
		'taken-down-suppressed',
		'email-request',
		'user-locked',
		'checkuser',
	];

	/**
	 * {@inheritdoc}
	 *
	 * @param ObjectManager $manager Object Manager
	 */
	public function load( ObjectManager $manager ) {
		foreach ( self::METADATA as $id ) {
			$metadata = new Metadata( [
				"id" => $id,
			] );

			$manager->persist( $metadata );
		}

		$manager->flush();
	}

}
