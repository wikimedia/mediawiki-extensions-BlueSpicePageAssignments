<?php

namespace BlueSpice\PageAssignments\Hook\BeforePageDisplay;

class AddDashboardResources extends \BlueSpice\Hook\BeforePageDisplay {

	protected function skipProcessing() {
		$title = $this->out->getTitle();
		$titles = [
			$title->equals( \SpecialPage::getTitleFor( "AdminDashboard" ) ),
			$title->equals( \SpecialPage::getTitleFor( "UserDashboard" ) ),
		];
		if ( !in_array( true, $titles ) ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$this->out->addModules( 'ext.bluespice.pageassignments.portlet' );
	}

}
