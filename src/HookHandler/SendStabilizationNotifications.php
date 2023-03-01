<?php

namespace BlueSpice\PageAssignments\HookHandler;

use BlueSpice\NotificationManager;
use BlueSpice\PageAssignments\AssignmentFactory;
use BlueSpice\PageAssignments\Notifications\PageReview;
use MediaWiki\Extension\ContentStabilization\Hook\Interfaces\ContentStabilizationStablePointAddedHook;
use MediaWiki\Extension\ContentStabilization\Hook\Interfaces\ContentStabilizationStablePointUpdatedHook;
use MediaWiki\Extension\ContentStabilization\StablePoint;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\Notifications\INotifier;
use TitleFactory;

/**
 * Echo/EchoConnector implementation, to be removed once Echo is removed
 */
class SendStabilizationNotifications implements
	ContentStabilizationStablePointAddedHook,
	ContentStabilizationStablePointUpdatedHook
{
	/** @var TitleFactory */
	protected $titleFactory;
	/** @var AssignmentFactory */
	protected $assignmentFactory;

	/** @var INotifier */
	protected $notifier;

	/**
	 *
	 * @param TitleFactory $titleFactory
	 * @param AssignmentFactory $assignmentFactory
	 * @param NotificationManager $notificationManager
	 */
	public function __construct(
		TitleFactory $titleFactory, AssignmentFactory $assignmentFactory,
		NotificationManager $notificationManager
	) {
		$this->titleFactory = $titleFactory;
		$this->assignmentFactory = $assignmentFactory;
		$this->notifier = $notificationManager->getNotifier();
	}

	/**
	 * @inheritDoc
	 */
	public function onContentStabilizationStablePointAdded( StablePoint $stablePoint ): void {
		$this->notify( $stablePoint );
	}

	/**
	 * @inheritDoc
	 */
	public function onContentStabilizationStablePointUpdated( StablePoint $updatedPoint ): void {
		$this->notify( $updatedPoint );
	}

	/**
	 * @param StablePoint $stablePoint
	 *
	 * @return void
	 */
	private function notify( StablePoint $stablePoint ) {
		$factory = MediaWikiServices::getInstance()->getService(
			'BSPageAssignmentsAssignmentFactory'
		);
		if ( !$factory ) {
			return;
		}
		$target = $factory->newFromTargetTitle( $stablePoint->getPage() );
		if ( !$target ) {
			return;
		}

		$notificationsManager = MediaWikiServices::getInstance()->getService( 'BSNotificationManager' );
		$notifier = $notificationsManager->getNotifier();

		$title = $this->titleFactory->castFromPageIdentity( $stablePoint->getPage() );
		$notification = new PageReview( $stablePoint->getApprover()->getUser(), $title, $target );
		$notifier->notify( $notification );
	}
}
