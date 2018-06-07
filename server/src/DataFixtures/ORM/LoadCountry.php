<?php

namespace App\DataFixtures\ORM;

use App\Entity\Country;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCountry implements FixtureInterface {

	/**
	 * {@inheritdoc}
	 *
	 * @param ObjectManager $manager Object Manager
	 */
	public function load( ObjectManager $manager ) {
		$countries = json_decode( file_get_contents( __DIR__ . '/../../../data/countries.json' ), true );
		foreach ( $countries as $data ) {
			$country = new Country( [
				'id' => $data['alpha-2'],
			] );

			$manager->persist( $country );
		}

		$manager->flush();
	}

}
