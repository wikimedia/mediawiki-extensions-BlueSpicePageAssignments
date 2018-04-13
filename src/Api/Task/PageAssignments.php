<?php

namespace BlueSpice\PageAssignments\Api\Task;

use BlueSpice\Services;
use BlueSpice\PageAssignments\IAssignment;

class PageAssignments extends \BSApiTasksBase {

	protected $sTaskLogType = 'bs-pageassignments';

	protected $aTasks = [
		'edit' => [
			'examples' => [
				[
					'pageId' => 152,
					'pageAssignments' => [
						'user/WikiSysop',
						'group/bot'
					]
				]
			],
			'params' => [
					'pageId' => [
						'desc' => 'ID of a page assignment is created for',
						'type' => 'integer',
						'required' => true
					],
					'pageAssignments' => [
						'desc' => 'Array of strings in form of "key/value", eg. "user/WikiSysop" or "group/sysop", can be empty',
						'type' => 'array',
						'required' => true
					]
			]
		],
		'getForPage' => [
			'examples' => [
				[
					'pageId' => 152
				]
			],
			'params' => [
				'pageId' => [
					'desc' => 'ID of a page to get assignments for',
					'type' => 'integer',
					'required' => true
				]
			]
		]
	];

	protected function getRequiredTaskPermissions() {
		return [
			'edit' => [ 'pageassignments' ],
			'getForPage' => [ 'read' ],
		];
	}

	protected function task_edit( $taskData, $params ) {
		$result = $this->makeStandardReturn();

		if( empty( $taskData->pageId ) ) {
			$taskData->pageId = 0;
		}
		$status = $this->getTargetFromID( $taskData->pageId );
		if( !$status->isOK() ) {
			$result->message = $status->getMessage()->parse();
			return $result;
		}
		$target = $status->getValue();

		$assignments = [];
		foreach( $taskData->pageAssignments as $id ) {
			//'user/WikiSysop' or 'group/bureaucrats'
			list( $type, $key ) = explode( '/', $id );
			if( empty( $type ) || empty( $key ) ) {
				continue;
			}
			$assignment = $this->getFactory()->factory(
				$type,
				$key,
				$target->getTitle()
			);
			if( !$assignment ) {
				continue;
			}
			$assignments[] = $assignment;
		}
		$status = $target->save( $assignments );
		if( !$status->isOK() ) {
			$result->message = $status->getMessage()->parse();
			return $result;
		}

		$removed = $target->diff(
			$target->getAssignments(),
			$status->getValue()->getAssignments()
		);
		$added = $target->diff(
			$status->getValue()->getAssignments(),
			$target->getAssignments()
		);
		$result->success = true;
		$this->logAssignmentChange(
			$target->getTitle(),
			$added,
			$removed
		);
		$this->notifyAssignmentChange(
			$target->getTitle(),
			$added,
			$removed
		);
		$this->runUpdates();

		return $result;
	}

	/**
	 * This is a convenience method. It could also be done by quering
	 * 'bs-pageassignment-store' with the right set of filters, but this one
	 * is much easier to access
	 * @param object $taskData
	 * @param array $params
	 * @return BSStandardAPIResponse
	 */
	protected function task_getForPage( $taskData, $params ) {
		$result = $this->makeStandardReturn();

		if( empty( $taskData->pageId ) ) {
			$taskData->pageId = 0;
		}
		$status = $this->getTargetFromID( $taskData->pageId );
		if( !$status->isOK() ) {
			$result->message = $status->getMessage();
			return $result;
		}
		$target = $status->getValue();

		$result->payload = [];
		foreach( $target->getAssignments() as $assignment ) {
			$result->payload[] = $assignment->toStdClass();
		}
		$result->success = true;

		return $result;
	}

	/**
	 *
	 * @param \Title $title
	 * @param IAssignment[] $addedAssignments
	 * @param IAssignment[] $removedAssignments
	 */
	public function logAssignmentChange( $title, $addedAssignments, $removedAssignments ) {
		foreach( $addedAssignments as $assignment ) {
			$this->logTaskAction(
				"add-{$assignment->getType()}",
				[ '4::editor' => $assignment->getKey() ],
				[ 'target' => $title ]
			);
		}
		foreach( $removedAssignments as $assignment ) {
			$this->logTaskAction(
				"remove-{$assignment->getType()}",
				[ '4::editor' => $assignment->getKey() ],
				[ 'target' => $title ]
			);
		}
	}

	public function notifyAssignmentChange( $title, $addedAssignments, $removedAssignments ) {
		$newUsers = [];
		$removedUsers = [];

		foreach( $addedAssignments as $assignment ) {
			$newUsers = array_merge(
				$newUsers,
				$assignment->getUserIds()
			);
		}

		foreach( $removedAssignments as $assignment ) {
			$removedUsers = array_merge(
				$removedUsers,
				$assignment->getUserIds()
			);
		}

		if( !empty( $newUsers ) ) {
			\BSNotifications::notify(
				"notification-bs-pageassignments-assignment-change-add",
				$this->getUser(),
				$title,
				array(
					'affected-users' => $newUsers
				)
			);
		}

		if( !empty( $removedUsers ) ) {
			\BSNotifications::notify(
				"notification-bs-pageassignments-assignment-change-remove",
				$this->getUser(),
				$title,
				array(
					'affected-users' => $removedUsers
				)
			);
		}
	}

	/**
	 *
	 * @return \BlueSpice\PageAssignments\AssignmentFactory
	 */
	protected function getFactory() {
		return Services::getInstance()->getService(
			'BSPageAssignmentsAssignmentFactory'
		);
	}

	/**
	 *
	 * @param integer $pageId
	 * @return \Status
	 */
	protected function getTargetFromID( $pageId ) {
		$title = \Title::newFromID( $pageId );
		if( !$title || !$title->exists() ) {
			return \Status::newFatal( 'bs-pageassignments-api-error-no-page' );
		}
		return $this->getTargetFromTitle( $title );
	}

	/**
	 *
	 * @param \Title $title
	 * @return \Status
	 */
	protected function getTargetFromTitle( \Title $title ) {
		if( !$target = $this->getFactory()->newFromTargetTitle( $title ) ) {
			return \Status::newFatal( 'bs-pageassignments-api-error-no-page' );
		}
		return \Status::newGood( $target );
	}

}