<?php

namespace BlueSpice\PageAssignments\Hook\SkinTemplateOutputPageBeforeExec;

use BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;
use BlueSpice\SkinData;

class AddAssignees extends SkinTemplateOutputPageBeforeExec {

	protected function skipProcessing() {
		if( $this->skin->getTitle()->getArticleID() < 1 ) {
			return true;
		}

		$factory = $this->getServices()->getService(
			'BSPageAssignmentsAssignmentFactory'
		);
		if( !$factory->newFromTargetTitle( $this->skin->getTitle() ) ) {
			return true;
		}
		$assignments = $factory->newFromTargetTitle(
			$this->skin->getTitle()
		)->getAssignments();

		if( count( $assignments ) < 1 ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {

		$icon = \Html::element( 'i', array(), '' );
		$html = \Html::rawElement(
			'a',
			[
				'href' => '#',
				'title' => wfMessage( 'bs-pageassignments-dlg-fldlabel' )->plain(),
				'data-graphicallist-callback' => 'pageassignments-list',
				'data-graphicallist-direction' => 'west'
			],
			$icon . wfMessage( 'bs-pageassignments-dlg-fldlabel' )->plain()
		);

		$this->mergeSkinDataArray(
			SkinData::PAGE_INFOS_PANEL,
			[
				'bs-pageassignments' => [
					'position' => 20,
					'label' => 'bs-pageassignments',
					'type' => 'html',
					'content' => $html
				]
			]
		);

		return true;
	}
}