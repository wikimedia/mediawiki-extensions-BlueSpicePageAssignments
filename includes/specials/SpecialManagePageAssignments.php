<?php

use BlueSpice\Special\ManagerBase;

class SpecialManagePageAssignments extends ManagerBase {
	/**
	 *
	 * @param string $name
	 * @param string $restriction
	 * @param bool $listed
	 * @param bool $function
	 * @param string $file
	 * @param bool $includable
	 */
	public function __construct( $name = '', $restriction = '', $listed = true,
		$function = false, $file = 'default', $includable = false ) {
		parent::__construct(
			'ManagePageAssignments',
			'pageassignments',
			$listed,
			$function,
			$file,
			$includable
		);
	}

	/**
	 * @return string ID of the HTML element being added
	 */
	protected function getId() {
		return 'bs-pageassignments-manager';
	}

	/**
	 * @return array
	 */
	protected function getModules() {
		return [
			'ext.pageassignments.manager'
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function getJSVars() {
		$aDeps = [];
		Hooks::run( 'BSPageAssignmentsManager', [ $this, &$aDeps ] );

		return [
			'bsPageAssignmentsManagerDeps' => $aDeps
		];
	}
}
