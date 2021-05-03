<?php

namespace BlueSpice\PageAssignments\Hook\PageMoveComplete;

use BlueSpice\Hook\PageMoveComplete;
use Title;

/**
 * Adapts assignments in case of article move.
 */
class MoveAssignments extends PageMoveComplete {

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		return Title::newFromLinkTarget( $this->new )->equals( $this->old );
	}

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->getServices()->getDBLoadBalancer( DB_PRIMARY )->update(
			'bs_pageassignments',
			[
				'pa_page_id' => Title::newFromLinkTarget( $this->new )->getArticleID()
			],
			[
				'pa_page_id' => Title::newFromLinkTarget( $this->old )->getArticleID()
			]
		);
		return true;
	}

}
