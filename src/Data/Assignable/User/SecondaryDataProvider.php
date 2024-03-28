<?php

namespace BlueSpice\PageAssignments\Data\Assignable\User;

use BlueSpice\PageAssignments\Data\Record;
use BlueSpice\PageAssignments\IAssignment;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\ISecondaryDataProvider;

class SecondaryDataProvider implements ISecondaryDataProvider {
	/**
	 * @param array $dataSets
	 * @return array
	 */
	public function extend( $dataSets ) {
		$assignmentFactory = MediaWikiServices::getInstance()->getService( 'BSPageAssignmentsAssignmentFactory' );
		$titleFactory = MediaWikiServices::getInstance()->getTitleFactory();
		$userFactory = MediaWikiServices::getInstance()->getUserFactory();

		$processed = [];
		foreach ( $dataSets as $record ) {
			$user = $userFactory->newFromName( $record->get( Record::TEXT ) );
			if ( !$user ) {
				continue;
			}

			$assignment = $assignmentFactory->factory(
				'user',
				$user->getName(),
				$titleFactory->newFromID( $record->get( Record::PAGE_ID ) )
			);
			if ( !$assignment instanceof IAssignment ) {
				// :(
				continue;
			}

			$processed[] = $assignment->getRecord();
		}

		return $processed;
	}
}
