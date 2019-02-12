<?php

namespace BlueSpice\PageAssignments;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\PageAssignments\Data\Record;
use BlueSpice\PageAssignments\Data\Assignment\Store;
use BlueSpice\Data\Filter;
use BlueSpice\Data\ReaderParams;
use BlueSpice\Context;
use BlueSpice\Services;
use BlueSpice\TargetCacheHandler;
use BlueSpice\TargetCache\Title\Target as CacheTarget;

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
	 *
	 * @param AssignableFactory $assignableFactory
	 * @param LinkRenderer $linkRenderer
	 */
	public function __construct( AssignableFactory $assignableFactory, LinkRenderer $linkRenderer, $config ) {
		$this->assignableFactory = $assignableFactory;
		$this->linkRenderer = $linkRenderer;
		$this->config = $config;
	}

	/**
	 *
	 * @param \Title $title
	 * @return boolean|Target
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
		//may support other targets than title in the future
		$instance = new Target(
			$this->config,
			$assignments,
			$title
		);

		$this->appendCache( $instance );
		return $instance;
	}

	/**
	 *
	 * @param Target $instance
	 */
	protected function appendCache( Target $instance ) {
		$this->targetCache[ $instance->getTitle()->getArticleId() ]
			= $instance;
		$this->getCache( $instance->getTitle() )->set( $instance );
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

		$this->targetCache[$title->getArticleID()] = $this->getCache( $title )->get();
		return $this->targetCache[$title->getArticleID()];
	}

	/**
	 *
	 * @return TargetCacheHandler
	 */
	private function getCache( \Title $title ) {
		return Services::getInstance()->getBSTargetCacheTitle()->getHandler(
			'pageassignments',
			new CacheTarget( $title )
		);
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
	public function invalidate( Target $target ) {
		if( isset( $this->targetCache[$target->getTitle()->getArticleID()] ) ) {
			unset( $this->targetCache[$target->getTitle()->getArticleID()] );
		}

		$this->getCache( $target->getTitle() )->invalidate();
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
