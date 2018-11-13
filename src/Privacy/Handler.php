<?php

namespace BlueSpice\PageAssignments\Privacy;

use BlueSpice\Privacy\IPrivacyHandler;

class Handler implements IPrivacyHandler {
	protected $user;
	protected $db;

	public function __construct( \User $user, \Database $db ) {
		$this->user = $user;
		$this->db = $db;
	}

	public function anonymize( $newUsername ) {
		$this->db->update(
			'bs_pageassignments',
			[ 'pa_assignee_key' => $newUsername ],
			[
				'pa_assignee_key' => $this->user->getName(),
				'pa_assignee_type' => 'user'
			]
		);

		return \Status::newGood();
	}
}