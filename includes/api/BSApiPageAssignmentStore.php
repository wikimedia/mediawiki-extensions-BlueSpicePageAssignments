<?php

use BlueSpice\Services;
use BlueSpice\Data\ReaderParams;
use BlueSpice\PageAssignments\Data\Record;

class BSApiPageAssignmentStore extends BSApiExtJSStoreBase {

	/**
	 *
	 * @param string $sQuery
	 * @return array
	 */
	protected function makeData( $sQuery = '' ) {
		$aResult = [];

		$aPageAssignments = $this->getPageAssignments();

		$res = $this->getDB()->select( 'page', '*' );
		foreach ( $res as $row ) {
			$oTitle = Title::newFromRow( $row );
			$oDataSet = (object)[
				'page_id' => $oTitle->getArticleID(),
				'page_prefixedtext' => $oTitle->getPrefixedText(),
				'assignments' => [],
				'page_namespace' => $oTitle->getNamespace()
			];

			// This is for better performance. For some reason PHP is very slow then accessing
			// $aPageAssignments[$oTitle->getArticleID()] directly
			if ( isset( $aPageAssignments[$oTitle->getArticleID()] ) ) {
				$oDataSet->assignments
					= $aPageAssignments[$oTitle->getArticleID()];
			}

			$aResult[$oTitle->getArticleID()] = $oDataSet;
		}

		return $aResult;
	}

	/**
	 *
	 * @param \stdClass $oFilter
	 * @param array $aDataSet
	 * @return bool
	 */
	public function filterString( $oFilter, $aDataSet ) {
		if ( $oFilter->field !== 'assignments' ) {
			return parent::filterString( $oFilter, $aDataSet );
		}

		$sFieldValue = '';
		foreach ( $aDataSet->assignments as $oAsignee ) {
			$sFieldValue .= $oAsignee->{Record::TEXT};
		}

		if ( empty( $sFieldValue ) ) {
			$sFieldValue = wfMessage( 'bs-pageassignments-no-assignments' )->plain();
		}

		return BsStringHelper::filter( $oFilter->comparison, $sFieldValue, $oFilter->value );
	}

	/**
	 *
	 * @return \stdClass[]
	 */
	protected function getPageAssignments() {
		$assignmentFactory = Services::getInstance()->getService(
			'BSPageAssignmentsAssignmentFactory'
		);
		$recordSet = $assignmentFactory->getStore()->getReader()->read(
			new ReaderParams( [
				ReaderParams::PARAM_LIMIT => ReaderParams::LIMIT_INFINITE
			] )
		);
		$assignments = [];
		foreach ( $recordSet->getRecords() as $record ) {
			$id = $record->get( Record::PAGE_ID );
			$assignments[$id][] = $assignmentFactory->factory(
				$record->get( Record::ASSIGNEE_TYPE ),
				$record->get( Record::ASSIGNEE_KEY ),
				\Title::newFromID( $id )
			)->toStdClass();
		}

		return $assignments;
	}

}
