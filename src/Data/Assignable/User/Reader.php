<?php

namespace BlueSpice\PageAssignments\Data\Assignable\User;

use MWStake\MediaWiki\Component\DataStore\ReaderParams;

class Reader extends \BlueSpice\Data\User\Reader {

	/**
	 *
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		return new PrimaryDataProvider( $this->db, $this->context );
	}

	/**
	 *
	 * @return null
	 */
	public function makeSecondaryDataProvider() {
		return null;
	}

}
