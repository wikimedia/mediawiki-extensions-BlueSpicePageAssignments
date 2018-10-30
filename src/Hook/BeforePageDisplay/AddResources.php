<?php

namespace BlueSpice\PageAssignments\Hook\BeforePageDisplay;

class AddResources extends \BlueSpice\Hook\BeforePageDisplay {

	protected function doProcess() {
		$this->out->addModuleStyles( 'ext.pageassignments.styles' );

		if ( $this->out->getRequest()->getVal( 'action', 'view') !== 'view' ) {
			return true;
		}
		if ( $this->out->getTitle()->isSpecialPage() ) {
			return true;
		}

		$this->out->addModules( 'ext.pageassignments.scripts' );
		return true;
	}

}
