<?php
namespace BlueSpice\PageAssignments\Api\Store;

use BlueSpice\PageAssignments\Data\Page\Store;
use BlueSpice\Context;

class Page extends \BlueSpice\Api\Store {

	protected function makeDataStore() {
		return new Store(
			new Context( $this->getContext(), $this->getConfig() ),
			$this->getServices()->getDBLoadBalancer()
		);
	}

}
