<?php

namespace BlueSpice\PageAssignments\Hook\PersonalUrls;

use BlueSpice\Hook\PersonalUrls;
use SpecialPage;
use SpecialPageFactory;

class AddMyPageAssignments extends PersonalUrls {

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		$user = $this->getContext()->getUser();
		return !$user->isLoggedIn();
	}

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->personal_urls['pageassignments'] = [
			'href' => SpecialPage::getTitleFor( 'PageAssignments' )->getLocalURL(),
			'text' => SpecialPageFactory::getPage( 'PageAssignments' )->getDescription()
		];
		return true;
	}

}
