<?php

namespace App\DataFixtures\ORM;

use App\Entity\Action;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMetadata implements FixtureInterface {

	const ACTION = [
		'yes',
		'no',
		'partial',
	];

	/**
	 * {@inheritdoc}
	 *
	 * @param ObjectManager $manager Object Manager
	 */
	public function load( ObjectManager $manager ) {
		foreach ( self::ACTION as $id ) {
			$action = new Action( [
				'id' => $id,
			] );

			$manager->persist( $action );
		}

		$manager->flush();
	}

}
