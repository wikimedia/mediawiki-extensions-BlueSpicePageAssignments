<?php

namespace BlueSpice\PageAssignments\Data\Assignable\Group;

use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\IPrimaryDataProvider;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use MWStake\MediaWiki\Component\DataStore\Record;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 *
	 * @var Record[]
	 */
	protected $data = [];

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @param \Wikimedia\Rdbms\IDatabase $db
	 * @param \IContextSource $context
	 */
	public function __construct( $db, $context ) {
		$this->context = $context;
		$this->db = $db;
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return array
	 */
	public function makeData( $params ) {
		$this->params = $params;
		$this->data = [];

		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig(
			'bsg'
		);

		$groupHelper = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )->getGroupHelper();
		$availableGroups = $groupHelper->getAvailableGroups();

		foreach ( $availableGroups as $groupname ) {
			$this->appendRowToData( $groupname );
		}

		return $this->data;
	}

	/**
	 *
	 * @param string $groupname
	 */
	protected function appendRowToData( $groupname ) {
		$assignmentFactory = MediaWikiServices::getInstance()->getService(
			'BSPageAssignmentsAssignmentFactory'
		);

		$assignment = $assignmentFactory->factory(
			'group',
			$groupname,
			$this->context->getTitle()
		);
		if ( !$assignment instanceof \BlueSpice\PageAssignments\IAssignment ) {
			// :(
			return;
		}

		if ( $this->params->getQuery() !== '' ) {
			$bApply = \BsStringHelper::filter(
				\BsStringHelper::FILTER_CONTAINS,
				$assignment->getKey(),
				$this->params->getQuery()
			) || \BsStringHelper::filter(
				\BsStringHelper::FILTER_CONTAINS,
				$assignment->getText(),
				$this->params->getQuery()
			);
			if ( !$bApply ) {
				return;
			}
		}

		$this->data[] = $assignment->getRecord();
	}
}
