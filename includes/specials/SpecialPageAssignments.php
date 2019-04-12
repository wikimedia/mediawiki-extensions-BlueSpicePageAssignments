<?php

class SpecialPageAssignments extends \BlueSpice\SpecialPage {
	public function __construct( $name = '', $restriction = '', $listed = true, $function = false, $file = 'default', $includable = false ) {
		parent::__construct( 'PageAssignments', $restriction, $listed, $function, $file, $includable );
	}

	public function execute( $sParameter ) {
		parent::execute( $sParameter );

		$this->getOutput()->addModules( 'ext.pageassignments.overview' );
		$aDeps = [];
		Hooks::run( 'BSPageAssignmentsOverview', [ $this, &$aDeps ] );
		$this->getOutput()->addJsConfigVars( 'bsPageAssignmentsOverviewDeps', $aDeps );
		$this->getOutput()->addHTML(
			Html::element( 'div', [ 'id' => 'bs-pageassignments-overview', 'class' => 'bs-manager-container' ] )
		);
	}

}
