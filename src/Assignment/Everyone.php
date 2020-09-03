<?php
namespace BlueSpice\PageAssignments\Assignment;

use MediaWiki\MediaWikiServices;

class Everyone extends \BlueSpice\PageAssignments\Assignment {

	/**
	 *
	 * @var int[]
	 */
	protected static $userIdCache = null;

	/**
	 *
	 * @return string
	 */
	protected function makeAnchor() {
		return \Html::element(
			'span',
			[ 'class' => 'bs-pa-special-everyone' ],
			$this->getText()
		);
	}

	/**
	 *
	 * @return string
	 */
	public function getText() {
		return \Message::newFromKey(
			'bs-pageassignments-assignee-special-everyone-label'
		)->plain();
	}

	/**
	 *
	 * @return int[]
	 */
	public function getUserIds() {
		if ( isset( static::$userIdCache ) ) {
			return static::$userIdCache;
		}
		static::$userIdCache = [];

		$loadBalancer = MediaWikiServices::getInstance()->getDBLoadBalancer();
		$res = $loadBalancer->getConnection( DB_REPLICA )->select(
			'user',
			'user_id'
		);
		$pm = MediaWikiServices::getInstance()->getPermissionManager();
		$title = $this->getTitle();
		foreach ( $res as $row ) {
			$allowed = $pm->userCan(
				'pageassignable',
				\User::newFromId( (int)$row->user_id ),
				$title
			);
			if ( !$allowed ) {
				continue;
			}
			static::$userIdCache[] = (int)$row->user_id;
		}

		return static::$userIdCache;
	}

}
