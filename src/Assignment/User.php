<?php
namespace BlueSpice\PageAssignments\Assignment;

class User extends \BlueSpice\PageAssignments\Assignment {

	protected function makeAnchor() {
		return $this->linkRenderer->makeLink(
			$this->getUser()->getUserPage(),
			new \HtmlArmor( $this->getText() )
		);
	}

	public function getText() {
		return \BsUserHelper::getUserDisplayName( $this->getUser() );
	}

	public function getUserIds() {
		return [ $this->getUser()->getId() ];
	}

	/**
	 *
	 * @return \User
	 */
	protected function getUser() {
		return \User::newFromName( $this->getKey() );
	}

}