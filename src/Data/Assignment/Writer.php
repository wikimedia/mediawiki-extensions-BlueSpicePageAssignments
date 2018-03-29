<?php

namespace BlueSpice\PageAssignments\Data\Assignment;
use BlueSpice\Data\FieldType;
use BlueSpice\Data\Filter;

class Writer extends \BlueSpice\Data\DatabaseWriter {

	/**
	 *
	 * @param \BlueSpice\Data\IReader $reader
	 * @param \LoadBalancer $loadBalancer
	 * @param \IContextSource $context
	 */
	public function __construct( \BlueSpice\Data\IReader $reader, $loadBalancer, \IContextSource $context = null ) {
		parent::__construct( $reader, $loadBalancer, $context, $context->getConfig() );
	}

	protected function getTableName() {
		return 'bs_pageassignments';
	}

	/**
	 * @return Schema Column definition compatible to
	 * https://docs.sencha.com/extjs/4.2.1/#!/api/Ext.grid.Panel-cfg-columns
	 */
	public function getSchema() {
		return new \BlueSpice\PageAssignments\Data\Schema();
	}

	protected function getIdentifierFields() {
		return [ Record::PAGE_ID, Record::ASSIGNEE_KEY, Record::ASSIGNEE_TYPE ];
	}
}
