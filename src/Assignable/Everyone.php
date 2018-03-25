<?php

namespace BlueSpice\PageAssignments\Assignable;

use BlueSpice\PageAssignments\Data\Assignable\Everyone\Store;
use BlueSpice\Context;
use BlueSpice\Services;

class Everyone extends \BlueSpice\PageAssignments\Assignable {

	public function getStore() {
		return new Store(
			$this->context,
			Services::getInstance()->getDBLoadBalancer()
		);
	}

	public function getAssignmentClass() {
		return "\\BlueSpice\\PageAssignments\\Assignment\\Everyone";
	}
}