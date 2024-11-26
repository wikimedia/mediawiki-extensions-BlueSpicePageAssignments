<?php

namespace BlueSpice\PageAssignments\Special;

use MediaWiki\Html\Html;
use MediaWiki\SpecialPage\SpecialPage;

class ManagePageAssignments extends SpecialPage {

	public function __construct() {
		parent::__construct( 'ManagePageAssignments' );
	}

	/**
	 * @inheritDoc
	 */
	public function execute( $subPage ) {
		parent::execute( $subPage );

		$out = $this->getOutput();
		$out->addModules( [	'ext.pageassignments.manager' ] );
		$out->addHTML( Html::element( 'div', [ 'id' => 'bs-pageassignments-manager' ] ) );
	}
}
