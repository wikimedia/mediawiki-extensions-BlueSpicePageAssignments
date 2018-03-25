<?php

namespace BlueSpice\PageAssignments;

class AssignableFactory {

	/**
	 *
	 * @var BlueSpice\IRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @param \BlueSpice\IRegistry $registry
	 * @param \Config $config
	 */
	public function __construct( $registry, $config ) {
		$this->registry = $registry;
		$this->config = $config;
	}

	/**
	 *
	 * @param string $type
	 * @param \IContextSource %context
	 * @return IAssignable
	 */
	public function factory( $type, \IContextSource $context = null ) {
		if( !$context ) {
			$context = \RequestContext::getMain();
		}
		$class = $this->registry->getValue(
			$type,
			false
		);
		if( !$class ) {
			throw new \MWException( "Assignee type '$type' not registered" );
		}
		return new $class(
			$context,
			$this->config,
			$type
		);
	}

	/**
	 *
	 * @param string $key
	 * @return array
	 */
	public function getRegisteredTypes() {
		return $this->registry->getAllKeys();
	}
}
