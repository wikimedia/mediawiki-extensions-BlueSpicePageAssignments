<?php
namespace BlueSpice\PageAssignments\Assignment;

use BlueSpice\Services;
use BlueSpice\TargetCache\Title\Target as Target;
use BsPageContentProvider;
use MediaWiki\MediaWikiServices as MediaWikiServices;
use Title;

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

		$loadBalancer = Services::getInstance()->getDBLoadBalancer();
		$res = $loadBalancer->getConnection( DB_REPLICA )->select(
			'user',
			[ 'user_id', 'user_name' ]
		);

		$blacklistedUsers = [];
		$pageName = 'PageAssignments-everyone-blacklist';
		$blacklistedUsersTitle = Title::makeTitle( NS_MEDIAWIKI, $pageName );

		if ( $blacklistedUsersTitle->exists() ) {

			$titleCache = MediaWikiServices::getInstance()->getService( 'BSTargetCacheTitle' );
			$target = new Target( $blacklistedUsersTitle );
			$cacheHandler = $titleCache->getHandler( 'pageassignments-everyone-blacklist', $target );
			$blacklistContent = $cacheHandler->get();
			if ( $blacklistContent === false ) {
				$blacklistContent = trim(
					BsPageContentProvider::getInstance()->getContentFromTitle( $blacklistedUsersTitle )
				);
				$cacheHandler->set( $blacklistContent );
			}
			if ( !empty( $blacklistContent ) ) {
				$blacklistedUsers = explode( "\n", $blacklistContent );
				foreach ( $blacklistedUsers as $k => $v ) {
					$blacklistedUsers[$k] = trim( $v );
				}
			}

		}

		foreach ( $res as $row ) {
			if ( in_array( $row->user_name, $blacklistedUsers ) ) {
				continue;
			}
			$allowed = $this->getTitle()->userCan(
				'pageassignable',
				\User::newFromId( (int)$row->user_id )
			);
			if ( !$allowed ) {
				continue;
			}
			static::$userIdCache[] = (int)$row->user_id;
		}
		return static::$userIdCache;
	}

}
