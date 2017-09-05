<?php

namespace App\Command;

use App\Entity\Takedown\Takedown;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportExisting extends Command {

	/**
	 * @var Registry
	 */
	protected $doctrine;

	/**
	 * @var Connection
	 */
	protected $old;

	/**
	 * {@inheritdoc}
	 *
	 * @param Registry $doctrine Doctrine.
	 * @param Connection $old Old Database Connection.
	 */
	public function __construct(
		Registry $doctrine,
		Connection $old
	) {
		$this->doctrine = $doctrine;
		$this->old = $old;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName( 'app:import:existing' )
			->setDescription( 'Import Existing Takedown Database' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param InputInterface $input Input
	 * @param OutputInterface $output Output
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$statement = $this->old->executeQuery("SELECT * FROM centrallog WHERE test = 'N'");
		$statement = $this->old->createQueryBuilder()
			->select( 'cl.*, dmca.*, cp.*, u.*' )
			->from( 'centrallog', 'cl' )
			->leftJoin( 'cl', 'dmcatakedowns', 'dmca', 'cl.id = dmca.log_id' )
			->leftJoin( 'cl', 'ncmecrelease', 'cp', 'cl.id = cp.log_id' )
			->leftJoin( 'cl', 'user', 'u', 'cl.user = u.user' )
			->where( "cl.test = 'N'" )
			->andWhere( "cl.type IN ('Child Protection', 'DMCA')" )
			->execute();

		$result = $statement->fetchAll( \PDO::FETCH_NAMED );
		foreach ( $result as $item ) {
			// Get the username.
			if ( !$item['wiki_user'] ) {
				switch ( $item['user'][0] ) {
					case 'kbrown':
						$item['wiki_user'] = 'Kbrown (WMF)';
						break;
					case 'ktsouroupidou':
						$item['wiki_user'] = 'Kalliope (WMF)';
				}
			}
			$username = $item['wiki_user'];

			$takedown = new Takedown( [
				'id' => (int)$item['id'][0],
				'reporter' => [
					'username' => $username,
				],
				'created' => new \DateTime( $item['timestamp'][0] ),
			] );

			// Get the Page Ids.
			$pageIds = [];
			if ( $item['files_affected'] ) {
				$urls = unserialize( $item['files_affected'] );
				if ( $urls ) {
					$pageIds = array_map( function ( $url ) {
						$pieces = explode( '/', parse_url( $url,  PHP_URL_PATH ) );
						return str_replace( ' ', '_', end( $pieces ) );
					}, $urls );
				}
			}
			$takedown->setPageIds( $pageIds );

			// Get Involved Users.
			$involvedNames = [];
			if ( $item['involved_user'] ) {
				$involvedNames = @unserialize( $item['involved_user'] );

				if ( $involvedNames === false ) {
					$involvedNames = [
						$item['involved_user'],
					];
				}
			} elseif ( $item['username'] ) {
				$involvedNames = [
					$item['username'],
				];
			}

			$takedown->setInvolvedNames( $involvedNames );

			switch ( $item['type'] ) {
				case 'Child Protection':
					break;
				case 'DMCA':
					break;
			}

			dump($item);
			dump($takedown);
		}
	}

}
