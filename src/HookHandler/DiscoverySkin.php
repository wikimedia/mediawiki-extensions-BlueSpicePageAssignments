<?php

namespace BlueSpice\PageAssignments\HookHandler;

use BlueSpice\Discovery\Hook\BlueSpiceDiscoveryTemplateDataProviderAfterInit;
use BlueSpice\Discovery\ITemplateDataProvider;
use BlueSpice\PageAssignments\GlobalActionsManager;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;

class DiscoverySkin implements
	MWStakeCommonUIRegisterSkinSlotComponents,
	BlueSpiceDiscoveryTemplateDataProviderAfterInit
{

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ): void {
		$registry->register(
			'GlobalActionsManager',
			[
				'ga-bluespice-pageassignments' => [
					'factory' => function () {
						return new GlobalActionsManager();
					}
				]
			]
		);
	}

	/**
	 *
	 * @param ITemplateDataProvider $registry
	 * @return void
	 */
	public function onBlueSpiceDiscoveryTemplateDataProviderAfterInit( $registry ): void {
		$registry->unregister( 'toolbox', 'ca-pageassignments' );
		$registry->register( 'actions_secondary', 'ca-pageassignments' );
	}
}
