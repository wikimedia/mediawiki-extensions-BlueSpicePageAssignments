<?php

namespace BlueSpice\PageAssignments\Tests;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;

/**
 * @group medium
 * @group Database
 * @group API
 * @group BlueSpice
 * @group BlueSpiceExtensions
 * @group BlueSpicePageAssignments
 * @covers BSApiMyPageAssignmentStore
 */
class BSApiMyPageAssignmentStoreTest extends BSApiExtJSStoreTestBase {

	/** @var int */
	protected $iFixtureTotal = 2;

	protected function getStoreSchema() {
		return [
			'page_id' => [
				'type' => 'integer'
			],
			'page_prefixedtext' => [
				'type' => 'string'
			],
			'page_link' => [
				'type' => 'string'
			],
			'assigned_by' => [
				'type' => 'array'
			]
		];
	}

	protected function createStoreFixtureData() {
		$dbw = $this->db;
		$this->setUp();

		$iPageID = $this->insertPage( "Test", "Dummy content" )['id'];

		$dbw->insert(
			'bs_pageassignments',
			[
				'pa_page_id' => 1,
				'pa_assignee_key' => 'sysop',
				'pa_assignee_type' => 'group',
				'pa_position' => 0
			],
			__METHOD__
		);
		$dbw->insert(
			'bs_pageassignments',
			[
				'pa_page_id' => $iPageID,
				'pa_assignee_key' => 'bureaucrat',
				'pa_assignee_type' => 'group',
				'pa_position' => 1
			],
			__METHOD__
		);
		$dbw->insert(
			'bs_pageassignments',
			[
				'pa_page_id' => $iPageID,
				'pa_assignee_key' => 'Apitestsysop',
				'pa_assignee_type' => 'user',
				'pa_position' => 2
			],
			__METHOD__
		);
		$dbw->insert(
			'bs_pageassignments',
			[
				'pa_page_id' => 1,
				'pa_assignee_key' => 'TestUser',
				'pa_assignee_type' => 'user',
				'pa_position' => 3
			],
			__METHOD__
		);
		return 2;
	}

	protected function getModuleName() {
		return 'bs-mypageassignment-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by page_prefixedtext' => [ 'string', 'eq', 'page_prefixedtext', 'UTPage', 1 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by page_prefixedtext and assigned_by' => [
				[
					[
						'type' => 'string',
						'comparison' => 'ct',
						'field' => 'page_prefixedtext',
						'value' => 'UT'
					],
					[
						'type' => 'integer',
						'comparison' => 'ct',
						'field' => 'assigned_by',
						'value' => 1
					]
				],
				1
			]
		];
	}

}
