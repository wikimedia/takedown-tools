<?php

namespace App\DataFixtures\ORM;

use App\Entity\ContentType;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadContentType implements FixtureInterface {

	const TYPES = [
		'file',
		'text',
	];

	/**
	 * {@inheritdoc}
	 *
	 * @param ObjectManager $manager Object Manager
	 */
	public function load( ObjectManager $manager ) {
		foreach ( self::TYPES as $id ) {
			$type = new ContentType( [
				'id' => $id,
			] );

			$manager->persist( $type );
		}

		$manager->flush();
	}

}
