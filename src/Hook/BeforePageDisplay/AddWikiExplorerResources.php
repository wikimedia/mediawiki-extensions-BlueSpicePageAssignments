<?php

namespace BlueSpice\PageAssignments\Hook\BeforePageDisplay;

class AddWikiExplorerResources extends \BlueSpice\Hook\BeforePageDisplay {

	protected function skipProcessing() {
		$wikiExplorer = \SpecialPage::getTitleFor( 'WikiExplorer' );
		if( !$wikiExplorer->equals( $this->out->getTitle() ) ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$this->out->addModules( 'ext.bluespice.pageassignments.wikiexplorer' );
		return true;
	}

}
