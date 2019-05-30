<?php

namespace BlueSpice\PageAssignments;

use BlueSpice\SMWConnector\PropertyValueProvider;
use BlueSpice\Services;
use User;
use SMW\DIWikiPage;

class PageAssignmentsPropertyValueProvider extends PropertyValueProvider {

	/**
	 *
	 * @var AssignmentFactory
	 */
	private $assignmentFactory = null;

	/**
	 *
	 * @param AssignmentFactory $assignmentFactory
	 */
	public function __construct( $assignmentFactory ) {
		$this->assignmentFactory = $assignmentFactory;
	}

	/**
	 *
	 * @return \BlueSpice\SMWConnector\IPropertyValueProvider[]
	 */
	public static function factory() {
		$factory = Services::getInstance()->getService( 'BSPageAssignmentsAssignmentFactory' );

		return [ new self( $factory ) ];
	}

	/**
	 *
	 * @return string
	 */
	public function getAliasMessageKey() {
		return "bs-pageassignments-sesp-pageassignments-alias";
	}

	/**
	 *
	 * @return string
	 */
	public function getDescriptionMessageKey() {
		return "bs-pageassignments-sesp-pageassignments-desc";
	}

	/**
	 *
	 * @return string
	 */
	public function getId() {
		return '_PAGEASSIGN';
	}

	/**
	 *
	 * @return string
	 */
	public function getLabel() {
		return "PageAssignments";
	}

	/**
	 *
	 * @param \SESP\AppFactory $appFactory
	 * @param \SMW\DIProperty $property
	 * @param \SMW\SemanticData $semanticData
	 * @return null
	 */
	public function addAnnotation( $appFactory, $property, $semanticData ) {
		$title = $semanticData->getSubject()->getTitle();
		if ( $title === null ) {
			return null;
		}

		$target = $this->assignmentFactory->newFromTargetTitle( $title );
		if ( $target === false ) {
			return null;
		}

		$userIds = $target->getAssignedUserIDs();
		foreach ( $userIds as $userId ) {
			$user = User::newFromId( $userId );
			$dataItem = DIWikiPage::newFromTitle( $user->getUserPage() );
			$semanticData->addPropertyObjectValue( $property, $dataItem );
		}

		return null;
	}
}
