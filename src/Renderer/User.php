<?php
namespace BlueSpice\PageAssignments\Renderer;

use BlueSpice\Renderer\Params;
use BlueSpice\Services;

class User extends Assignment {
	const PARAM_ASSIGNMENT = 'assignment';

	/**
	 *
	 * @var IAssignment
	 */
	protected $assignment = null;

	/**
	 *
	 * @param mixed $val
	 * @return mixed
	 */
	protected function render_image( $val ) {
		$renderer = Services::getInstance()->getBSRendererFactory()->get(
			'userimage',
			new Params( [
				'user' => \User::newFromName( $this->assignment->getKey() )
			] )
		);
		return $renderer->render();
	}

}
