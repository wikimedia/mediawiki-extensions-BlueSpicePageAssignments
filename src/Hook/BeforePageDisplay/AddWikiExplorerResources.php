<?php

namespace BlueSpice\PageAssignments\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;
use SpecialPage;

class AddWikiExplorerResources extends BeforePageDisplay {

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		if ( !$this->getServices()->getSpecialPageFactory()->exists( 'WikiExplorer' ) ) {
			return true;
		}
		$title = $this->out->getTitle();
		if ( $title && !$title->equals( SpecialPage::getTitleFor( 'WikiExplorer' ) ) ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$this->out->addModules( 'ext.bluespice.pageassignments.wikiexplorer' );
		return true;
	}

}
