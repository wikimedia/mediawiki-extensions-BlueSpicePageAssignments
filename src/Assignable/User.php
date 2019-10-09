<?php

namespace BlueSpice\PageAssignments\Assignable;

use BlueSpice\PageAssignments\Data\Assignable\User\Store;
use BlueSpice\Services;

class User extends \BlueSpice\PageAssignments\Assignable {

	/**
	 *
	 * @return Store
	 */
	public function getStore() {
		return new Store(
			$this->context,
			Services::getInstance()->getDBLoadBalancer()
		);
	}

	/**
	 *
	 * @return string
	 */
	public function getAssignmentClass() {
		return "\\BlueSpice\\PageAssignments\\Assignment\\User";
	}

	/**
	 *
	 * @return string
	 */
	public function getRendererKey() {
		return "assignment-user";
	}

	/**
	 *
	 * @return string
	 */
	public function getTypeMessageKey() {
		return "bs-pageassignments-assignee-type-user";
	}

}
