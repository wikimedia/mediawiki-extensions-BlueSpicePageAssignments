<?php

use BlueSpice\ExtensionAttributeBasedRegistry;
use MediaWiki\MediaWikiServices;

return [

	'BSPageAssignmentsAssignmentFactory' => static function ( MediaWikiServices $services ) {
		$assignable = $services->getService(
			'BSPageAssignmentsAssignableFactory'
		);

		$targetRegistry = new ExtensionAttributeBasedRegistry(
			'BlueSpicePageAssignmentsTargetRegistry'
		);

		return new \BlueSpice\PageAssignments\AssignmentFactory(
			$assignable,
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			$targetRegistry
		);
	},

	'BSPageAssignmentsAssignableFactory' => static function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpicePageAssignmentsTypeRegistry'
		);

		return new \BlueSpice\PageAssignments\AssignableFactory(
			$registry,
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},
];
