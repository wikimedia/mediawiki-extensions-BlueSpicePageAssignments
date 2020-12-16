<?php

namespace BlueSpice\PageAssignments\Notifications\PresentationModel;

use BlueSpice\EchoConnector\EchoEventPresentationModel;

class AssignmentChangeRemove extends EchoEventPresentationModel {
	/**
	 * Gets appropriate messages keys and params
	 * for header message
	 *
	 * @return array
	 */
	public function getHeaderMessageContent() {
		$bundleKey = '';
		$bundleParams = [];

		$headerKey = 'notification-bs-pageassignments-assignment-change-remove-summary';
		// double title due to backwards compatibility with older translations
		// when the parameter documentions were wrong Bug:T151597
		$headerParams = [ 'agent', 'title', 'title' ];

		if ( $this->distributionType == 'email' ) {
			$headerKey = 'notification-bs-pageassignments-assignment-change-remove-subject';
		}

		return [
			'key' => $headerKey,
			'params' => $headerParams,
			'bundle-key' => $bundleKey,
			'bundle-params' => $bundleParams
		];
	}

	/**
	 * Gets appropriate message key and params for
	 * web notification message
	 *
	 * @return array
	 */
	public function getBodyMessageContent() {
		$bodyKey = 'notification-bs-pageassignments-assignment-change-remove-body';
		// double title due to backwards compatibility with older translations
		// when the parameter documentions were wrong Bug:T151597
		$bodyParams = [ 'agent', 'title', 'title' ];

		if ( $this->distributionType == 'email' ) {
			$bodyKey = 'notification-bs-pageassignments-assignment-change-remove-body';
		}

		return [
			'key' => $bodyKey,
			'params' => $bodyParams
		];
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
