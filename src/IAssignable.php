<?php
namespace BlueSpice\PageAssignments;

Interface IAssignable {

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @retrun \BlueSpice\Data\IStore
	 */
	public function getStore();

	/**
	 * @return string
	 */
	public function getAssignmentClass();

	/**
	 * @return string
	 */
	public function getRendererKey();
}