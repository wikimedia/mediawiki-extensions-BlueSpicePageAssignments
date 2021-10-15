<?php

namespace BlueSpice\PageAssignments\Data\DataCollector\AssignedPages;

use BlueSpice\Data\Record as BaseRecord;
use BlueSpice\PageAssignments\Entity\Collection\AssignedPages;

class Record extends BaseRecord {
	public const ASSIGNED = AssignedPages::ATTR_ASSIGNED_PAGES;
	public const UNASSIGNED = AssignedPages::ATTR_UNASSIGNED_PAGES;
	public const ASSIGNED_AGGREGATED = AssignedPages::ATTR_ASSIGNED_PAGES_AGGREGATED;
	public const UNASSIGNED_AGGREGATED = AssignedPages::ATTR_UNASSIGNED_PAGES_AGGREGATED;
	public const NAMESPACE_NAME = AssignedPages::ATTR_NAMESPACE_NAME;
}
