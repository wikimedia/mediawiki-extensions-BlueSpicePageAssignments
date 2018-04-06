<?php

namespace BlueSpice\PageAssignments\ConfigDefinition;

class Permissions extends \BlueSpice\ConfigDefinition\ArraySetting {

	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_ADMINISTRATION . '/BlueSpicePageAssignments',
			static::MAIN_PATH_EXTENSION . '/BlueSpicePageAssignments/' . static::FEATURE_ADMINISTRATION,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/BlueSpicePageAssignments',
		];
	}

	public function getOptions() {
		$permissions = array_diff(
			\User::getAllRights(),
			$this->getConfig()->get( 'UIPermissionBlacklist' )
		);
		return array_combine( $permissions, $permissions );
	}

	public function getLabelMessageKey() {
		return 'bs-authors-pref-limit';
	}
}
