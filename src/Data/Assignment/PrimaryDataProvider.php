<?php

namespace BlueSpice\PageAssignments\Data\Assignment;

use \BlueSpice\Data\IPrimaryDataProvider;
use BlueSpice\Data\FilterFinder;
use BlueSpice\Data\Filter;
use BlueSpice\PageAssignments\Data\Schema;
use BlueSpice\PageAssignments\Data\Record;

class PrimaryDataProvider implements IPrimaryDataProvider {

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
	 * @param \Wikimedia\Rdbms\IDatabase $db
	 */
	public function __construct( $params, $db ) {
		$this->db = $db;
	}

	/**
	 *
	 * @param \BlueSpice\Data\ReaderParams $params
	 */
	public function makeData( $params ) {
		$this->data = [];

		$res = $this->db->select(
			'bs_pageassignments',
			'*'
		);
		foreach( $res as $row ) {
			$this->appendRowToData( $row );
		}

		return $this->data;
	}

	protected function appendRowToData( $row ) {
		$this->data[] = new Record( (object) [
			Record::PAGE_ID => $row->{Record::PAGE_ID},
			Record::ASSIGNEE_KEY => $row->{Record::ASSIGNEE_KEY},
			Record::ASSIGNEE_TYPE => $row->{Record::ASSIGNEE_TYPE},
			Record::POSITION => $row->{Record::POSITION},
		] );
	}
}
