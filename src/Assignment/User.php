<?php

namespace BlueSpice\PageAssignments\Assignment;

use MediaWiki\MediaWikiServices;
use MediaWiki\User\User as MediaWikiUser;

class User extends \BlueSpice\PageAssignments\Assignment {

	/**
	 *
	 * @return string
	 */
	protected function makeAnchor() {
		return $this->linkRenderer->makeLink(
			$this->getUser()->getUserPage(),
			new \HtmlArmor( $this->getText() )
		);
	}

	/**
	 *
	 * @return string
	 */
	public function getText() {
		$utilities = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' );
		return $utilities->getUserHelper( $this->getUser() )->getDisplayName();
	}

	/**
	 *
	 * @return int[]
	 */
	public function getUserIds() {
		if ( $this->getUser()->getId() < 1 ) {
			return [];
		}
		return [ $this->getUser()->getId() ];
	}

	/**
	 *
	 * @return MediaWikiUser
	 */
	protected function getUser() {
		$user = MediaWikiServices::getInstance()->getUserFactory()
			->newFromName( $this->getKey() );
		if ( !$user ) {
			return new MediaWikiUser;
		}
		return $user;
	}

}
