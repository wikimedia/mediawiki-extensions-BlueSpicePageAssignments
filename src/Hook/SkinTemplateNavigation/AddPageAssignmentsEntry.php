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
		if ( !\MediaWiki\MediaWikiServices::getInstance()->getPermissionManager()
			->userCan(
				'pageassignments',
				$this->sktemplate->getUser(),
				$this->sktemplate->getTitle()
			)
		) {
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
