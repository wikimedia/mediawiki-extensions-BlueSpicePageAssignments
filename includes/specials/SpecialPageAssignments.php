<?php

use BlueSpice\Special\ManagerBase;

class SpecialPageAssignments extends ManagerBase {
	public function __construct( $name = '', $restriction = '', $listed = true, $function = false, $file = 'default', $includable = false ) {
		parent::__construct( 'PageAssignments', $restriction, $listed, $function, $file, $includable );
	}

	/**
	 * @return string ID of the HTML element being added
	 */
	protected function getId() {
		return 'bs-pageassignments-overview';
	}

	/**
	 * @return array
	 */
	protected function getModules() {
		return [
			'ext.pageassignments.overview'
		];
	}

	protected function getJSVars() {
		$aDeps = [];
		Hooks::run( 'BSPageAssignmentsOverview', [ $this, &$aDeps ] );
		return [
			'bsPageAssignmentsOverviewDeps' => $aDeps
		];
	}
}
