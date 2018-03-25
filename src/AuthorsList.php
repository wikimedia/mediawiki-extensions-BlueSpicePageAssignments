<?php

namespace BlueSpice\Authors;

class AuthorsList {

	/**
	 *
	 * @var \Wikimedia\Rdbms\LoadBalancer
	 */
	protected $loadBalancer = null;

	/**
	 *
	 * @var \Title
	 */
	protected $title = null;

	/**
	 *
	 * @var string[]
	 */
	protected $blacklist = [];


	/**
	 *
	 * @var int
	 */
	protected $limit = 1;

	/**
	 *
	 * @var bool
	 */
	protected $more = false;

	/**
	 *
	 * @param \Title $title
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 */
	public function __construct( $title, $blacklist, $limit, $loadBalancer = null ) {
		$this->title = $title;
		$this->blacklist = $blacklist;
		$this->loadBalancer = $loadBalancer;
		$this->limit = $limit;

		if( $this->loadBalancer === null ) {
			$services = \MediaWiki\MediaWikiServices::getInstance();
			$this->loadBalancer = $services->getDBLoadBalancer();
		}
	}

	/**
	 * Find first editor. If editor is on blacklist return empty string.
	 * @param string $originator
	 * @param Revision $revision
	 * @return string The originators username
	 *
	 */
	public function getOriginator( $revision ) {
		if( $revision instanceof \Revision === false ) {
			return '';
		}
		$originator = $revision->getUserText();
		if ( \User::isIP( $originator ) ) {
			return '';
		}
		if( in_array( $originator, $this->blacklist ) ) {
			return '';
		}
		return $originator;

	}

	/**
	 * @return string[]
	 */
	public function getEditors() {
		$usertexts = $this->loadAllUserTexts();

		if( empty( $usertexts ) ) {
			return [];
		}

		$count = count( $usertexts );
		$items = 0;
		$editors = [];

		while ( $items < $count ) {
			if ( $items > ( $this->limit - 1 ) ) {
				$this->more = true;
				break;
			}

			if ( \User::isIP( $usertexts[$items] ) ) {
				unset( $usertexts[$items] );
				$items++;
				continue;
			}

			$user = \User::newFromName( $usertexts[$items] );

			if ( !is_object( $user ) || in_array( $user->getName(), $this->blacklist ) ) {
				unset( $usertexts[$items] );
				$items++;
				continue;
			}

			$editors[] = $usertexts[$items];
			$items++;
		}

		return $editors;
	}

	public function moreEditors() {
		return $this->more;
	}

	protected function loadAllUserTexts() {
		$dbr = $this->loadBalancer->getConnection( DB_REPLICA );
		$res = $dbr->select(
			array( 'revision' ),
			array( 'rev_user_text', 'MAX(rev_timestamp) AS ts' ),
			array( 'rev_page' => $this->title->getArticleID() ),
			__METHOD__,
			array(
				'GROUP BY' => 'rev_user_text',
				'ORDER BY' => 'ts DESC'
			)
		);

		if ( $res->numRows() == 0 ) {
			return [];
		}

		$authors = [];
		foreach ( $res as $row ) {
			$authors[] = $row->rev_user_text;
		}

		$dbr->freeResult( $res );

		return $authors;
	}
}