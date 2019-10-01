<?php

namespace WebImage\Db;

class ManagerConfig {
	/** @var string|null */
	private $tablePrefix;

	/**
	 * GlobalTableConfig constructor.
	 *
	 * @param string|null $tablePrefix
	 */
	public function __construct($tablePrefix='')
	{
		$this->tablePrefix = $tablePrefix;
	}

	/**
	 * @return string
	 */
	public function getTablePrefix()/* : string */
	{
		return $this->tablePrefix;
	}
}