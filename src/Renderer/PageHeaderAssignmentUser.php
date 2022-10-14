<?php
namespace BlueSpice\PageAssignments\Renderer;

use User;

class PageHeaderAssignmentUser extends PageHeaderAssignmentBase {
	public const PARAM_ASSIGNMENT = 'assignment';

	/**
	 *
	 * @var IAssignment
	 */
	protected $assignment = null;

	/**
	 * Returns an array of arguments
	 * @return array
	 */
	public function getArgs() {
		$args = parent::getArgs();

		$user = User::newFromName( $this->assignment->getKey() );

		$util = $this->services->getService( 'BSUtilityFactory' );

		$userLink = $this->linkRenderer->makeLink(
			$user->getUserPage(),
			$util->getUserHelper( $user )->getDisplayName()
		);

		$args['text'] = $userLink;
		return $args;
	}
}
