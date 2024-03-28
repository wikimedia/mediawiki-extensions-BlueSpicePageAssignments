<?php

namespace BlueSpice\PageAssignments\Data\Assignable\User;

use MWStake\MediaWiki\Component\DataStore\Filter\StringValue;
use MWStake\MediaWiki\Component\DataStore\ReaderParams as Base;

class ReaderParams extends Base {

	/**
	 * @param Base $params
	 */
	public function __construct( Base $params ) {
		parent::__construct();
		$this->query = $params->getQuery();
		$this->sort = $params->getSort();
		$this->start = $params->getStart();
		$this->limit = $params->getLimit();
		$this->filter = $params->getFilter();
	}

	/**
	 * @return array|\MWStake\MediaWiki\Component\DataStore\Filter[]
	 */
	public function getFilter() {
		$filters = [];
		foreach ( $this->filter as $filter ) {
			if ( $filter->getField() === 'text' ) {
				$filters[] = new StringValue( [
					'field' => 'user_name',
					'value' => $filter->getValue(),
					'comparison' => $filter->getComparison()
				] );
			} else {
				$filters[] = $filter;
			}
		}

		return $filters;
	}
}
