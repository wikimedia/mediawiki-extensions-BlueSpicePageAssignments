<?php

namespace BlueSpice\PageAssignments\HookHandler;

use BlueSpice\PageAssignments\GlobalActionsManager;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;

class CommonUserInterface implements MWStakeCommonUIRegisterSkinSlotComponents {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ): void {
		$registry->register(
			'GlobalActionsManager',
			[
				'ga-bluespice-pageassignments' => [
					'factory' => static function () {
						return new GlobalActionsManager();
					}
				]
			]
		);
	}

}
