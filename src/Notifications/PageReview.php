<?php

namespace BlueSpice\PageAssignments\Notifications;

use BlueSpice\BaseNotification;
use BlueSpice\PageAssignments\ITarget;
use Title;
use User;

class PageReview extends BaseNotification {
	protected $target;

	/**
	 *
	 * @param User $agent
	 * @param Title $title
	 * @param ITarget $target
	 */
	public function __construct( $agent, $title, $target ) {
		parent::__construct( 'bs-pageassignments-page-approval', $agent, $title );

		$this->target = $target;
		$this->addAffectedUsersFromTarget();
		$this->extra['assignment-sources'] = $target->getAssignedUserIDs();
	}

	/**
	 *
	 * @return array
	 */
	public function getParams() {
		return [
			'titlelink' => true
		];
	}

	protected function addAffectedUsersFromTarget() {
		$affectedUsers = [];
		foreach ( $this->target->getAssignedUserIDs() as $userId ) {
			$affectedUsers[] = $userId;
		}

		$this->addAffectedUsers( $affectedUsers );
	}
}
