<?php
namespace BlueSpice\PageAssignments\Renderer;

use BlueSpice\Renderer\Params;

class User extends Assignment {
	public const PARAM_ASSIGNMENT = 'assignment';

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
		$renderer = $this->services->getService( 'BSRendererFactory' )->get(
			'userimage',
			new Params( [
				'user' => \User::newFromName( $this->assignment->getKey() )
			] )
		);
		return $renderer->render();
	}

}
