<?php

namespace BlueSpice\PageAssignments\Statistics\Report;

use BlueSpice\ExtendedStatistics\ClientReportHandler;
use BlueSpice\ExtendedStatistics\IReport;

class AssignedPages implements IReport {

	/**
	 * @inheritDoc
	 */
	public function getSnapshotKey() {
		return 'pa-assignedpages';
	}

	/**
	 * @inheritDoc
	 */
	public function getClientData( $snapshots, array $filterData, $limit = 20 ): array {
		$dataset = null;
		if ( isset( $filterData['namespaces'] ) && !empty( $filterData['namespaces'] ) ) {
			$dataset = 'namespace';
			$filterValues = $filterData['namespaces'];
			$filterValues = array_map( static function ( $id ) {
				if ( (int)$id === 0 ) {
					return '-';
				}
				return \MWNamespace::getCanonicalName( $id );
			}, $filterValues );
		}
		if ( isset( $filterData['categories'] ) && !empty( $filterData['categories'] ) ) {
			$dataset = 'categories';
			$filterValues = array_map( static function ( $cat ) {
				return str_replace( ' ', '_', $cat );
			}, $filterData['categories'] );
		}

		$processed = [];
		foreach ( $snapshots as $snapshot ) {
			$data = $snapshot->getData();
			if ( $dataset === null ) {
				$processed[] = [
					'name' => $snapshot->getDate()->forGraph(),
					'assigned' => $data['assigned'],
					'unassigned' => $data['unassigned'],
				];
				continue;
			}
			$data = $data[$dataset];
			foreach ( $data as $key => $details ) {
				if ( !in_array( $key, $filterValues ) ) {
					continue;
				}
				$processed[] = [
					'name' => $snapshot->getDate()->forGraph(),
					'assigned' => $details['assigned'],
					'unassigned' => $details['unassigned'],
				];
			}
		}

		return $processed;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientReportHandler(): ClientReportHandler {
		return new ClientReportHandler(
			[ 'ext.bluespice.pageassignments.statistics' ],
			'bs.pageAssignments.report.AssignedPagesReport'
		);
	}
}
