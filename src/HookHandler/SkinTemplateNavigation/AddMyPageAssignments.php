<?php

namespace BlueSpice\PageAssignments\HookHandler\SkinTemplateNavigation;

use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use MediaWiki\MediaWikiServices;
use SpecialPage;

class AddMyPageAssignments implements SkinTemplateNavigation__UniversalHook {

	/**
	 * // phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
	 * @inheritDoc
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		$user = $sktemplate->getUser();
		if ( !$user->isRegistered() ) {
			return;
		}

		$links['user-menu']['pageassignments'] = [
			'id' => 'pt-pageassignments',
			'href' => SpecialPage::getTitleFor( 'PageAssignments' )->getLocalURL(),
			'text' => MediaWikiServices::getInstance()->getSpecialPageFactory()
				->getPage( 'PageAssignments' )->getDescription(),
			'position' => 60,
		];
	}
}
