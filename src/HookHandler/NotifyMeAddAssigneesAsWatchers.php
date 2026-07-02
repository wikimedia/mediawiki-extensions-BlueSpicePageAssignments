<?php

namespace BlueSpice\PageAssignments\HookHandler;

use BlueSpice\PageAssignments\AssignmentFactory;
use MediaWiki\Extension\NotifyMe\Hook\NotifyMeWatchlistProviderGetWatchersHook;
use MediaWiki\Extension\NotifyMe\Hook\NotifyMeWatchlistProviderGetWatchSourceHook;
use MediaWiki\Message\Message;
use MediaWiki\Title\Title;
use MediaWiki\User\UserFactory;
use MWStake\MediaWiki\Component\Events\Delivery\IChannel;
use MWStake\MediaWiki\Component\Events\INotificationEvent;
use MWStake\MediaWiki\Component\Events\ITitleEvent;
use MWStake\MediaWiki\Component\Events\Notification;

class NotifyMeAddAssigneesAsWatchers implements
	NotifyMeWatchlistProviderGetWatchersHook,
	NotifyMeWatchlistProviderGetWatchSourceHook
{

	/** @var array */
	private array $assignees = [];

	/**
	 * @param AssignmentFactory $assignmentFactory
	 * @param UserFactory $userFactory
	 */
	public function __construct(
		private readonly AssignmentFactory $assignmentFactory,
		private readonly UserFactory $userFactory
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function onNotifyMeWatchlistProviderGetWatchSource( Notification $notification, Message &$description ) {
		if ( !( $notification->getEvent() instanceof ITitleEvent ) ) {
			return;
		}
		$title = $notification->getEvent()->getTitle();
		$this->setTitleAssignees( $title );
		$assignees = $this->assignees[$title->getPrefixedDBkey()] ?? [];
		foreach ( $assignees as $user ) {
			if ( $user->getId() === $notification->getTargetUser()->getId() ) {
				$description = new Message( 'bs-pageassignments-notifications-watchlist-description' );
				return;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function onNotifyMeWatchlistProviderGetWatchers(
		INotificationEvent $event, IChannel $channel, array &$watchers
	): void {
		if ( !( $event instanceof ITitleEvent ) ) {
			return;
		}
		$title = $event->getTitle();
		$this->setTitleAssignees( $title );
		$watchers = array_merge( $watchers, $this->assignees[$title->getPrefixedDBkey()] ?? [] );
	}

	/**
	 * @param Title $title
	 * @return void
	 */
	private function setTitleAssignees( Title $title ): void {
		if ( isset( $this->assignees[$title->getPrefixedDBkey()] ) ) {
			return;
		}
		$target = $this->assignmentFactory->newFromTargetTitle( $title );
		if ( !$target ) {
			return;
		}
		$assignees = [];
		foreach ( $target->getAssignedUserIDs() as $uid ) {
			$assignees[] = $this->userFactory->newFromId( $uid );
		}

		$this->assignees[$title->getPrefixedDBkey()] = $assignees;
	}
}
