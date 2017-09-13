<?php

namespace App\Command;

use App\Entity\Takedown\Takedown;
use App\Entity\Takedown\ChildProtection\ChildProtection;
use App\Entity\Takedown\Dmca\Dmca;
use App\Util\TakedownUtilInterface;
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
	 * @var TakedownUtilInterface
	 */
	protected $takedownUtil;

	/**
	 * @var string[]
	 */
	protected const SITE_IDS = [
		'en.wikipedia.org' => 'enwiki',
		'wikimediafoundation.org' => 'foundationwiki',
		'cy.wikipedia.org' => 'cywiki',
		'en.wiktionary.org' => 'enwiktionary',
		'commons.wikimedia.org' => 'commonswiki',
	];

	/**
	 * @var string[]
	 */
	protected const PROJECT_IDS = [
		'Wikimedia Commons' => 'commonswiki',
	];

	/**
	 * @var string[]
	 */
	protected const USER_NAMES = [
		'kbrown' => 'Kbrown (WMF)',
		'ktsouroupidou' => 'Kalliope (WMF)',
	];

	/**
	 * @var string[]
	 */
	protected const APPROVER_NAMES = [
		'Geoff Brigham' => 'GeoffBrigham (WMF)',
		'Geoff' => 'GeoffBrigham (WMF)',
		'Jacob Rogers' => 'Jrogers (WMF)',
		'Jacob' => 'Jrogers (WMF)',
		'Luis Villa' => 'LuisV (WMF)',
		'Luis' => 'LuisV (WMF)',
		'Stephen LaPorte' => 'Slaporte (WMF)',
		'Stephen' => 'Slaporte (WMF)',
	];

	/**
	 * @var string[]
	 */
	protected const METADATA_IDS = [
		'Checkuser data was available and is being included below.' => 'checkuser',
		'An email was sent to legal@rt.wikimedia.org with the file name asking for it to be deleted.' => 'email-request',
		'The content was taken down and we have awareness of facts or circumstances from which infringing activity is apparent. ' => 'taken-down-apparent',
		'The content was taken down pursuant to a DMCA notice.' => 'taken-down-dmca',
		'The content was taken down and we have actual knowledge that the content was infringing copyright ' => 'taken-down-infringing',
		'The content was taken down and suppressed.' => 'taken-down-suppressed',
		'The content was taken down and the user was clearly warned and discouraged from future violations.' => 'taken-down-user-warned',
		'The user who uploaded the content has been locked.' => 'user-locked',
	];

	/**
	 * {@inheritdoc}
	 *
	 * @param Registry $doctrine Doctrine.
	 * @param Connection $old Old Database Connection.
	 * @param TakedownUtilInterface $takedownUtil Takedown Utility.
	 */
	public function __construct(
		Registry $doctrine,
		Connection $old,
		TakedownUtilInterface $takedownUtil
	) {
		$this->doctrine = $doctrine;
		$this->old = $old;
		$this->takedownUtil = $takedownUtil;

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
	 *
	 * @return void
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$statement = $this->old->executeQuery( "SELECT * FROM centrallog WHERE test = 'N'" );
		$statement = $this->old->createQueryBuilder()
			->select( 'cl.*, dmca.*, cp.*, u.*' )
			->from( 'centrallog', 'cl' )
			->leftJoin( 'cl', 'dmcatakedowns', 'dmca', 'cl.id = dmca.log_id' )
			->leftJoin( 'cl', 'ncmecrelease', 'cp', 'cl.id = cp.log_id' )
			->leftJoin( 'cl', 'user', 'u', 'cl.user = u.user' )
			->where( "cl.test = 'N'" )
			->andWhere( "cl.type IN ('Child Protection', 'DMCA')" )
			->execute();

		$promises = [];

		$result = $statement->fetchAll( \PDO::FETCH_NAMED );
		foreach ( $result as $item ) {
			// Get the username.
			if ( !$item['wiki_user'] ) {
				$item['wiki_user'] = self::USER_NAMES[$item['user'][0]];
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
			$siteId = null;
			if ( $item['files_affected'] ) {
				$urls = unserialize( $item['files_affected'] );
				if ( $urls ) {
					$pageIds = array_map( function ( $url ) {
						$pieces = explode( '/', parse_url( $url,  PHP_URL_PATH ) );
						return str_replace( ' ', '_', end( $pieces ) );
					}, $urls );

					$domain = parse_url( $urls[0],  PHP_URL_HOST );
					$siteId = self::SITE_IDS[$domain];
					$takedown->setSiteId( $siteId );
				}
			}

			if ( $item['project'] ) {
				$takedown->setSiteId( self::PROJECT_IDS[$item['project']] );
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

			$metadataIds = [];
			if ( $item['logging_metadata'] ) {
				$metadataIds = array_reduce( $item['logging_metadata'], function ( $carry, $item ) {
					if ( !$item ) {
						return $carry;
					}

					$names = unserialize( $item );

					if ( !$names ) {
						return $carry;
					}

					$ids = array_map( function ( $item ) {
						return self::METADATA_IDS[$item];
					}, $names );

					return array_merge( $carry, $ids );
				}, [] );
			}
			$takedown->setMetadataIds( $metadataIds );

			switch ( $item['type'] ) {
				case 'Child Protection':
					$cp = new ChildProtection( [
						'approved' => $item['legalapproved'] === 'Y' ? true : false,
						'deniedApprovalReason' => $item['whynotapproved'] ?: null,
						'ncmecId' => (int)$item['report_id'],
						'comments' => $item['logging_details'] ?: null,
						'files' => [
							[
								'name' => $item['filename'],
							],
						],
					] );

					if ( $item['whoapproved'] ) {
						$cp->setApproverName( self::APPROVER_NAMES[$item['whoapproved']] );
					}

					$takedown->setCp( $cp );
					break;
				case 'DMCA':
					// Get the Lumen Id.
					$lumenId = null;
					if ( $item['ce_url'] ) {
						$path = explode( '/', parse_url( $item['ce_url'], PHP_URL_PATH ) );
						$lumenId = intval( end( $path ) );
					}

					$wmfTitle = null;
					if ( $item['wmfwiki_title'] ) {
						$wmfTitle = str_replace( ' ', '_', $item['wmfwiki_title'] );
					}

					$sent = null;
					if ( $item['takedown_date'] ) {
						$sent = new \DateTime( $item['takedown_date'] );
					}

					$dmca = new Dmca( [
						'takedown' => $takedown,
						'lumenId' => $lumenId,
						'lumenTitle' => $item['takedown_title'],
						'method' => $item['takedown_method'],
						'subject' => $item['takedown_subject'],
						'sent' => $sent,
						'senderCity' => $item['sender_city'],
						'senderState' => $item['sender_state'],
						'senderZip' => $item['sender_zip'],
						'wmfTitle' => $wmfTitle,
					] );

					if ( $item['sender_country'] ) {
						$dmca->setSenderCountryCode( $item['sender_country'] );
					}

					if ( $item['action_taken'] ) {
						$dmca->setActionTakenId( strtolower( $item['action_taken'] ) );
					}

					$takedown->setDmca( $dmca );
					break;
			}

			$promises[] = $this->takedownUtil->create( $takedown )
				->then( function( $takedown ) use ( $output ) {
					$output->writeln( 'Saved Takedown #' . $takedown->getId() );
					return $takedown;
				} );
		}

		return \GuzzleHttp\Promise\all( $promises )->wait();
	}

}
