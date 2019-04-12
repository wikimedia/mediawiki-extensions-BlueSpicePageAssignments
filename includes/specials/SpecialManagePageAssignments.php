<?php

class SpecialManagePageAssignments extends \BlueSpice\SpecialPage {
	public function __construct( $name = '', $restriction = '', $listed = true, $function = false, $file = 'default', $includable = false ) {
		parent::__construct( 'ManagePageAssignments', 'pageassignments', $listed, $function, $file, $includable );
	}

	public function execute( $sParameter ) {
		parent::execute( $sParameter );

		$this->getOutput()->addModules( 'ext.pageassignments.manager' );
		$aDeps = [];
		Hooks::run( 'BSPageAssignmentsManager', [ $this, &$aDeps ] );
		$this->getOutput()->addJsConfigVars( 'bsPageAssignmentsManagerDeps', $aDeps );
		$this->getOutput()->addHTML(
			Html::element( 'div', [ 'id' => 'bs-pageassignments-manager', 'class' => 'bs-manager-container' ] )
		);
	}

}
