<?php

use BlueSpice\Services;
use BlueSpice\Data\ReaderParams;
use BlueSpice\PageAssignments\Data\Record;

class BSApiMyPageAssignmentStore extends BSApiExtJSStoreBase {

	protected function makeData($sQuery = '') {

		$aAssignments = $this->getPageAssignments();

		$aResult = [];
		foreach( $aAssignments as $pageId => $pageAssignmentRelations ) {
			foreach( $pageAssignmentRelations as $assignment ) {
				$assigned = in_array(
					$this->getUser()->getId(),
					$assignment->getUserIds()
				);
				if( !$assigned ) {
					continue;
				}

				$link = Services::getInstance()->getLinkRenderer()->makeLink(
					$assignment->getTitle()
				);
				$oDataSet = (object)array(
					'page_id' => $assignment->getTitle()->getArticleID(),
					'page_prefixedtext' => $assignment->getTitle()->getPrefixedText(),
					'page_link' => $link,
					'assigned_by' => $assignment->toStdClass(),
				);
				$aResult[] = $oDataSet;
			}
		}
		return $aResult;
	}

	public function filterString($oFilter, $aDataSet) {
		if( $oFilter->field !== 'assigned_by') {
			return parent::filterString($oFilter, $aDataSet);
		}

		$sFieldValue = '';
		foreach( $aDataSet->assigned_by as $oAsignee ) {
			if( $oAsignee->{Record::ASSIGNEE_TYPE} == 'user' ) {
				$sFieldValue .= wfMessage( 'bs-pageassignments-directly-assigned' )->plain();
			}
			else {
				$sFieldValue .= $oAsignee->text;
			}
		}

		return BsStringHelper::filter( $oFilter->comparison, $sFieldValue, $oFilter->value );
	}

	/**
	 *
	 * @return \BlueSpice\PageAssignments\IAssignment[]
	 */
	protected function getPageAssignments() {
		$assignmentFactory = Services::getInstance()->getService(
			'BSPageAssignmentsAssignmentFactory'
		);
		$recordSet = $assignmentFactory->getStore()->getReader()->read(
			new ReaderParams( [] )
		);
		$assignments = [];
		foreach( $recordSet->getRecords() as $record ) {
			$id = $record->get( Record::PAGE_ID );
			$assignments[$id][] = $assignmentFactory->factory(
				$record->get( Record::ASSIGNEE_TYPE ),
				$record->get( Record::ASSIGNEE_KEY ),
				\Title::newFromID( $id )
			);
		}
		return $assignments;
	}
}