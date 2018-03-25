<?php
namespace BlueSpice\PageAssignments;

use BlueSpice\Data\RecordSet;

class Target {

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @var RecordSet
	 */
	protected $assignments = null;

	/**
	 *
	 * @var string
	 */
	protected $title = null;

	public function __construct( $config, array $assignments, \Title $title ) {
		$this->config = $config;
		$this->assignments = $assignments;
		$this->title = $title;
	}

	public function getAssignments() {
		return $this->assignments;
	}

	public function getTitle() {
		return $this->title;
	}

}