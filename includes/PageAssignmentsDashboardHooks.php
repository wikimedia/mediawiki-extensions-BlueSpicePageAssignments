<?php

class PageAssignmentsDashboardHooks {

	/**
	 * Hook Handler for BSDashboardsUserDashboardPortalPortlets
	 *
	 * @param array &$aPortlets reference to array portlets
	 * @return boolean always true to keep hook alive
	 */
	public static function onBSDashboardsUserDashboardPortalPortlets( &$aPortlets ) {
		$aPortlets[] = array(
			'type'  => 'BS.PageAssignments.portlets.PageAssignmentsPortlet',
			'config' => array(
				'title' => wfMessage(
					'bs-pageassignments-yourassignments'
				)->plain(),
			),
			'title' => wfMessage(
				'bs-pageassignments-yourassignments'
			)->plain(),
			'description' => wfMessage(
				'bs-pageassignments-yourassignmentsdesc'
			)->plain(),
		);

		return true;
	}

	/**
	 * Hook Handler for BSDashboardsUserDashboardPortalConfig
	 *
	 * @param object $oCaller caller instance
	 * @param array &$aPortalConfig reference to array portlet configs
	 * @param boolean $bIsDefault default
	 * @return boolean always true to keep hook alive
	 */
	public static function onBSDashboardsUserDashboardPortalConfig( $oCaller, &$aPortalConfig, $bIsDefault ) {
		$aPortalConfig[0][] = array(
			'type' => 'BS.PageAssignments.portlets.PageAssignmentsPortlet',
			'config' => array(
				'title' => wfMessage(
					'bs-pageassignments-yourassignments'
				)->plain(),
			),
		);

		return true;
	}
}