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
	protected $iFixtureTotal = 1;

	protected function getStoreSchema() {
		return [
			'page_id' => [
				'type' => 'numeric'
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
		$dbw = $this->getDb();
		$this->setUp();

		$iPageID = $this->insertPage( "Test", "Dummy content" )['id'];

		$dbw->insert(
			'bs_pageassignments',
			[
				'pa_page_id' => $iPageID,
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
				'pa_page_id' => $iPageID,
				'pa_assignee_key' => 'TestUser',
				'pa_assignee_type' => 'user',
				'pa_position' => 3
			],
			__METHOD__
		);
		return 1;
	}

	protected function getModuleName() {
		return 'bs-mypageassignment-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by page_prefixedtext' => [ 'string', 'eq', 'page_prefixedtext', 'notExist', 0 ],
			'Filter by page_prefixedtext' => [ 'string', 'eq', 'page_prefixedtext', 'Test', 1 ]
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
						'value' => 'Te'
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
