<?php

namespace BlueSpice\PageAssignments\Data\Assignable\Everyone;

use BlueSpice\Services;

class PrimaryDataProvider implements \BlueSpice\Data\IPrimaryDataProvider {

	/**
	 *
	 * @var \BlueSpice\Data\Record[]
	 */
	protected $data = [];

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @var \BlueSpice\Data\ReaderParams
	 */
	protected $params = null;

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
	 * @param \BlueSpice\Data\ReaderParams $params
	 */
	public function makeData( $params ) {
		$this->params = $params;
		$this->data = [];

		$this->appendRowToData();

		return $this->data;
	}

	protected function appendRowToData() {
		$assignmentFactory = Services::getInstance()->getService(
			'BSPageAssignmentsAssignmentFactory'
		);

		$assignment = $assignmentFactory->factory(
			'specialeveryone',
			'everyone',
			$this->context->getTitle()
		);
		if( !$assignment instanceof \BlueSpice\PageAssignments\IAssignment ) {
			return; //:(
		}
		if( $this->params->getQuery() !== '' ) {
			$query = strtolower( $this->params->getQuery() );
			$bApply =
				strpos(
					strtolower( $assignment->getKey() ),
					$query
				) !== false
				|| strpos(
					strtolower( $assignment->getText() ),
					$query
				) !== false;

			if( !$bApply ) {
				return;
			}
		}

		$this->data[] = $assignment->getRecord();
	}
}

