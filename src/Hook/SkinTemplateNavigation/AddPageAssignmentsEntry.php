<?php

namespace BlueSpice\PageAssignments\Hook\SkinTemplateNavigation;

use BlueSpice\Hook\SkinTemplateNavigation;

class AddPageAssignmentsEntry extends SkinTemplateNavigation {
	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		if ( !$this->sktemplate->getRequest()->getVal( 'action', 'view' ) !== 'view' ) {
			return true;
		}
		if ( !$this->sktemplate->getTitle()->userCan( 'pageassignments' ) ) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->links['actions']['pageassignments'] = [
			'text' => $this->msg( 'bs-pageassignments-menu-label' )->text(),
			'href' => '#',
			'class' => false,
			'id' => 'ca-pageassignments',
			'bs-group' => 'hidden'
		];
		return true;
	}

}
