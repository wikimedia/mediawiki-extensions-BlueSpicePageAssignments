<?php

namespace BlueSpice\PageAssignments;
use BlueSpice\ExtensionAttributeBasedRegistry;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\PageAssignments\Data\Record;
use BlueSpice\PageAssignments\Data\Assignment\Store;
use BlueSpice\Data\Filter;
use BlueSpice\Data\ReaderParams;
use BlueSpice\Context;
use BlueSpice\Services;

class AssignmentFactory {

	/**
	 *
	 * @var IAssignment[]
	 */
	protected $targetCache = [];

	/**
	 *
	 * @var AssignableFactory
	 */
	protected $assignableFactory = null;

	/**
	 *
	 * @var LinkRenderer
	 */
	protected $linkRenderer = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $targetRegistry;

	/**
	 *
	 * @param AssignableFactory $assignableFactory
	 * @param LinkRenderer $linkRenderer
	 * @param \Config bsgConfig
	 * @param ExtensionAttributeBasedRegistry $targetRegistry
	 *
	 */
	public function __construct( AssignableFactory $assignableFactory, LinkRenderer $linkRenderer, $config, $targetRegistry ) {
		$this->assignableFactory = $assignableFactory;
		$this->linkRenderer = $linkRenderer;
		$this->config = $config;
		$this->targetRegistry = $targetRegistry;
	}

	/**
	 *
	 * @param \Title $title
	 * @return boolean|Target
	 * @throws \MWException
	 */
	public function newFromTargetTitle( \Title $title ) {
		if( $title->getArticleID() < 1 ) {
			return false;
		}
		$instance = $this->fromCache( $title );
		if ( $instance ) {
			return $instance;
		}

		$assignments = $this->getAssignments( $title );
		$targetClass = $this->getTargetClass();
		if ( $targetClass === null ) {
			throw new \MWException( 'No target specified' );
		}

		$instance = call_user_func_array( "$targetClass::factory", [
			$this->config,
			$assignments,
			$title
		] );

		$this->appendCache( $instance );
		return $instance;
	}

	protected function getTargetClass() {
		$targets = $this->targetRegistry->getAllKeys();
		$targetToUse = $this->config->get( 'PageAssignmentsTarget' );

		if ( $targetToUse && in_array( $targetToUse, $targets ) ) {
			return $this->targetRegistry->getValue( $targetToUse );
		}
		return null;
	}

	/**
	 *
	 * @param Target $instance
	 */
	protected function appendCache( ITarget $instance ) {
		$this->targetCache[ $instance->getTitle()->getArticleId() ]
			= $instance;
	}

	/**
	 *
	 * @param \Title $title
	 * @return Target|false
	 */
	protected function fromCache( \Title $title ) {
		if( isset( $this->targetCache[$title->getArticleID()] ) ) {
			return $this->targetCache[$title->getArticleID()];
		}
		return false;
	}

	/**
	 *
	 * @param \Title $title
	 * @return IAssignment[]
	 */
	protected function getAssignments( \Title $title = null ) {
		if( !$title || $title->getArticleID() < 1 ) {
			return [];
		}

		$recordSet = $this->getStore()->getReader()->read(
			new ReaderParams( [ 'filter' => [
				[
					Filter::KEY_FIELD => Record::PAGE_ID,
					Filter::KEY_VALUE => (int) $title->getArticleID(),
					Filter::KEY_TYPE => 'numeric',
					Filter::KEY_COMPARISON => Filter::COMPARISON_EQUALS,
				]
			]] )
		);

		$assignments = [];
		foreach( $recordSet->getRecords() as $record ) {
			$assignment = $this->factory(
				$record->get( Record::ASSIGNEE_TYPE ),
				$record->get( Record::ASSIGNEE_KEY ),
				$title
			);
			if( !$assignment ) {
				continue;
			}
			$assignments[] = $assignment;
		}
		return $assignments;
	}

	public function getStore() {
		return new Store(
			new Context( \RequestContext::getMain(), $this->config ),
			Services::getInstance()->getDBLoadBalancer()
		);
	}

	/**
	 *
	 * @param Target $target
	 * @return true
	 */
	public function invalidate( ITarget $target ) {
		if( isset( $this->targetCache[$target->getTitle()->getArticleID()] ) ) {
			unset( $this->targetCache[$target->getTitle()->getArticleID()] );
		}
		return true;
	}

	/**
	 *
	 * @param string $type
	 * @return IAssignment | null
	 */
	public function factory( $type, $key, \Title $title ) {
		if( !$assignable = $this->assignableFactory->factory( $type ) ) {
			return null;
		}
		$class = $assignable->getAssignmentClass();

		return new $class(
			$this->config,
			$this->linkRenderer,
			$title,
			$type,
			$key
		);
	}

	/**
	 *
	 * @param string $key
	 * @return array
	 */
	public function getRegisteredTypes() {
		return $this->assignableFactory->getRegisteredTypes();
	}
}
