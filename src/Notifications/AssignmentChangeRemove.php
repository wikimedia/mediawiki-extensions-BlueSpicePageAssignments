<?php

namespace BlueSpice\PageAssignments\Notifications;

use BlueSpice\BaseNotification;

class AssignmentChangeRemove extends BaseNotification {
	public function __construct( $agent, $title, $affectedUsers ) {
		parent::__construct( 'bs-pageassignments-assignment-change-remove', $agent, $title );
		$this->addAffectedUsers( $affectedUsers );
	}

	public function getParams() {
		return [
			'titlelink' => true
		];
	}
}
