<?php

namespace BlueSpice\PageAssignments\Data\Assignable\User;

use BlueSpice\Data\User\Record;
use BlueSpice\PageAssignments\IAssignment;
use GlobalVarConfig;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use MWStake\MediaWiki\Component\DataStore\Schema;
use Title;
use Wikimedia\Rdbms\IDatabase;

class PrimaryDataProvider extends \MWStake\MediaWiki\Component\CommonWebAPIs\Data\UserQueryStore\PrimaryDataProvider {

	/**
	 * @var null | ReaderParams
	 */
	private $params = null;

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 * @param IDatabase $db
	 * @param Schema $schema
	 * @param GlobalVarConfig $mwsgConfig
	 * @param Title $title
	 */
	public function __construct( IDatabase $db, Schema $schema, GlobalVarConfig $mwsgConfig, Title $title ) {
		parent::__construct( $db, $schema, $mwsgConfig );
		$this->title = $title;
	}

	/**
	 * @param ReaderParams $params
	 *
	 * @return \MWStake\MediaWiki\Component\DataStore\Record[]
	 */
	public function makeData( $params ) {
		$this->params = $params;
		return parent::makeData( $params );
	}

	/**
	 *
	 * @param \stdClass $row
	 */
	protected function appendRowToData( $row ) {
		if ( $this->params->getQuery() !== '' ) {
			$bApply = \BsStringHelper::filter(
				\BsStringHelper::FILTER_CONTAINS,
				$row->{Record::USER_NAME},
				$this->params->getQuery()
			) || \BsStringHelper::filter(
				\BsStringHelper::FILTER_CONTAINS,
				$row->{Record::USER_REAL_NAME},
				$this->params->getQuery()
			);
			if ( !$bApply ) {
				return;
			}
		}

		$services = MediaWikiServices::getInstance();
		$user = $services->getUserFactory()->newFromId( $row->{Record::ID} );
		if ( !$user ) {
			return;
		}

		if ( !$services->getPermissionManager()
			->userCan( 'pageassignable', $user, $this->title )
		) {
			return;
		}
		$assignmentFactory = $services->getService( 'BSPageAssignmentsAssignmentFactory' );
		$assignment = $assignmentFactory->factory(
			'user',
			$row->{Record::USER_NAME},
			$this->title
		);
		if ( !$assignment instanceof IAssignment ) {
			// :(
			return;
		}

		$this->data[] = $assignment->getRecord();
	}
}
