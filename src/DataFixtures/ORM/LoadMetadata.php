<?php

namespace App\DataFixtures\ORM;

use App\Entity\Metadata;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMetadata implements FixtureInterface {

	const METADATA = [
		'taken-down-user-warned' => 'The content was taken down and the user was '
																. 'clearly warned and discouraged from future violations.',
		'taken-down-infringing' => 'The content was taken down and we have actual '
																. 'knowledge that the content was infringing copyright ',
		'taken-down-apparent' => 'The content was taken down and we have awareness of '
															. 'facts or circumstances from which infringing activity is apparent.',
		'taken-down-dmca' => 'The content was taken down pursuant to a DMCA notice.',
		'taken-down-suppressed' => 'The content was taken down and suppressed.',
		'email-request' => 'An email was sent to legal@rt.wikimedia.org with the '
												. 'file name asking for it to be deleted.',
		'user-locked' => 'The user who uploaded the content has been locked.',
		'checkuser' => 'Checkuser data was available and is being included below.',
	];

	/**
	 * {@inheritdoc}
	 *
	 * @param ObjectManager $manager Object Manager
	 */
	public function load( ObjectManager $manager ) {
		foreach ( self::METADATA as $id => $content ) {
			$metadata = new Metadata( [
				'id' => $id,
				'content' => $content,
			] );

			$manager->persist( $metadata );
		}

		$manager->flush();
	}

}
