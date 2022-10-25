<?php

namespace BlueSpice\PageAssignments\HookHandler\SkinTemplateNavigation;

use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use MediaWiki\MediaWikiServices;
use RequestContext;
use SpecialPage;

class AddMyPageAssignments implements SkinTemplateNavigation__UniversalHook {

	/**
	 * // phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
	 * @inheritDoc
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		$user = RequestContext::getMain()->getUser();
		if ( !$user->isRegistered() ) {
			return;
		}

		$links['pageassignments'] = [
			'href' => SpecialPage::getTitleFor( 'PageAssignments' )->getLocalURL(),
			'text' => MediaWikiServices::getInstance()->getSpecialPageFactory()
				->getPage( 'PageAssignments' )->getDescription(),
			'position' => 60,
		];
	}
}
