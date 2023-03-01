<?php

namespace BlueSpice\PageAssignments\Notifications\PresentationModel;

use BlueSpice\EchoConnector\EchoEventPresentationModel;

class PageReview extends EchoEventPresentationModel {
	/**
	 * Gets appropriate messages keys and params
	 * for header message
	 *
	 * @return array
	 */
	public function getHeaderMessageContent() {
		$bundleKey = '';
		$bundleParams = [];

		$headerKey = 'bs-pageassignments-notification-pagereview-summary';

		if ( $this->distributionType == 'email' ) {
			$headerKey = 'bs-pageassignments-notification-pagereview-subject';
		}

		return [
			'key' => $headerKey,
			'params' => [ 'agent', 'title', 'title' ],
			'bundle-key' => '',
			'bundle-params' => []
		];
	}

	/**
	 * Gets appropriate message key and params for
	 * web notification message
	 *
	 * @return array
	 */
	public function getBodyMessageContent() {
		return [
			'key' => 'bs-pageassignments-notification-pagereview-body',
			'params' => [ 'agent', 'title', 'title' ],
		];
	}

	/**
	 *
	 * @return \Message
	 */
	public function getBodyMessage() {
		$content = $this->getBodyMessageContent();
		$msg = $this->msg( $content['key'] );
		if ( empty( $content['params'] ) ) {
			return $msg;
		}

		foreach ( $content['params'] as $param ) {
			$this->paramParser->parseParam( $msg, $param );
		}

		return $msg;
	}

	/**
	 *
	 * @return array
	 */
	public function getSecondaryLinks() {
		if ( $this->isBundled() ) {
			// For the bundle, we don't need secondary actions
			return [];
		}

		return [ $this->getAgentLink() ];
	}
}
