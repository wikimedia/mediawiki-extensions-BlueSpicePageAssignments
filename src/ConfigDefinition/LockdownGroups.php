<?php

namespace BlueSpice\PageAssignments\ConfigDefinition;

class LockdownGroups extends \BlueSpice\ConfigDefinition\GroupList {

	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_ADMINISTRATION . '/BlueSpicePageAssignments',
			static::MAIN_PATH_EXTENSION . '/BlueSpicePageAssignments/' . static::FEATURE_ADMINISTRATION,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/BlueSpicePageAssignments',
		];
	}

	public function getLabelMessageKey() {
		return 'bs-pageassignments-pref-lockdowngroups';
	}

	public function isHidden() {
		return !$this->config->get( 'PageAssignmentsUseAdditionalPermissions' );
	}
}
