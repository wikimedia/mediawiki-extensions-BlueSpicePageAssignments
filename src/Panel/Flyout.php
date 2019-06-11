<?php

namespace BlueSpice\PageAssignments\Panel;

use Message;
use BlueSpice\Calumma\Panel\BasePanel;
use BlueSpice\Calumma\IFlyout;

class Flyout extends BasePanel implements IFlyout {

	/**
	 * @return Message
	 */
	public function getFlyoutTitleMessage() {
		return wfMessage( 'bs-pageassignments-flyout-title' );
	}

	/**
	 * @return Message
	 */
	public function getFlyoutIntroMessage() {
		return wfMessage( 'bs-pageassignments-flyout-intro' );
	}

	/**
	 * @return Message
	 */
	public function getTitleMessage() {
		return wfMessage( 'bs-pageassignments-nav-link-title-pageassignments' );
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return '';
	}

	/**
	 *
	 * @return string
	 */
	public function getTriggerCallbackFunctionName() {
		return 'bs.pageassignments.flyoutCallback';
	}

	/**
	 *
	 * @return array
	 */
	public function getTriggerRLDependencies() {
		return [ 'ext.bluespice.pageassignments.flyout' ];
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @return bool
	 */
	public function shouldRender( $context ) {
		return $context->getUser()->isLoggedIn();
	}
}
