<?php

use BlueSpice\PageAssignments\AssignmentFactory;
use MediaWiki\MediaWikiServices;

class BSApiMyPageAssignmentStore extends BSApiExtJSStoreBase {
	/** @var AssignmentFactory  */
	private $assignmentFactory;
	private $linkRenderer;
	private $titles;

	/**
	 * @param ApiMain $mainModule
	 * @param string $moduleName
	 * @param string $modulePrefix
	 */
	public function __construct( ApiMain $mainModule, $moduleName, $modulePrefix = '' ) {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );

		$this->assignmentFactory = MediaWikiServices::getInstance()->getService(
			'BSPageAssignmentsAssignmentFactory'
		);
		$this->linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
	}

	/**
	 *
	 * @param string $sQuery
	 * @return array
	 */
	protected function makeData( $sQuery = '' ) {
		$assignments = $this->getAssignmentsForUser( $this->getUser() );

		$aResult = $assignedBy = [];
		foreach ( $assignments as $pageId => $pageAssignments ) {
			foreach ( $pageAssignments as $assignment ) {
				$assignedBy[$pageId][] = $assignment;
			}
		}
		foreach ( $assignedBy as $pageId => $relatedAssignments ) {
			if ( !isset( $this->titles[$pageId] ) ) {
				continue;
			}
			$title = $this->titles[$pageId];
			$oDataSet = (object)[
				'page_id' => $title->getArticleID(),
				'page_prefixedtext' => $title->getPrefixedText(),
				'page_link' => '',
				'assigned_by' => [],
				'assignment' => [],
			];
			foreach ( $relatedAssignments as $assignment ) {
				$oDataSet->assigned_by[] = $assignment->getType();
				$oDataSet->assignment[] = $assignment->toStdClass();
			}
			$aResult[] = $oDataSet;
		}
		return $aResult;
	}

	/**
	 * @inheritDoc
	 */
	public function postProcessData( $aData ) {
		$data = parent::postProcessData( $aData );
		foreach ( $data as $dataSet ) {
			if ( !isset( $this->titles[$dataSet->page_id] ) ) {
				continue;
			}
			$dataSet->page_link = $this->linkRenderer->makeLink(
				$this->titles[$dataSet->page_id]
			);
		}

		return $aData;
	}

	/**
	 *
	 * @param \stdClass $oFilter
	 * @param array $aDataSet
	 * @return bool
	 */
	public function filterString( $oFilter, $aDataSet ) {
		if ( $oFilter->field !== 'assigned_by' ) {
			return parent::filterString( $oFilter, $aDataSet );
		}

		$sFieldValue = '';
		foreach ( $aDataSet->assigned_by as $oAssignee ) {
			$sFieldValue .= $oAssignee->text;
		}

		return BsStringHelper::filter( $oFilter->comparison, $sFieldValue, $oFilter->value );
	}

	/**
	 * @param User $user
	 * @return array
	 */
	protected function getAssignmentsForUser( User $user ) {
		$db = $this->getDB();
		$registeredTypes = $this->assignmentFactory->getRegisteredTypes();

		$res = $db->select(
			[ 'pa' => 'bs_pageassignments', 'p' => 'page' ],
			[ 'pa.*', 'p.page_id', 'p.page_title', 'p.page_namespace' ],
			[
				'pa.pa_assignee_type IN (' . $db->makeList( $registeredTypes ) . ')',
				'pa.pa_assignee_key' => $user->getName()
			],
			__METHOD__,
			[],
			[
				// INNER JOIN to ensure that page exists
				'p' => [ 'INNER JOIN', [ 'p.page_id = pa.pa_page_id' ] ]
			]
		);

		$assignments = [];
		foreach ( $res as $row ) {
			$title = \Title::newFromRow( $row );
			$this->titles[$title->getArticleID()] = $title;
			$assignments[$title->getArticleID()][] = $this->assignmentFactory->factory(
				$row->pa_assignee_type,
				$row->pa_assignee_key,
				$title
			);
		}

		return $assignments;
	}
}
