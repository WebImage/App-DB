<?php

namespace WebImage\Db;

class ManagerConfig {
	/** @var string|null */
	private ?string $tablePrefix;

	/**
	 * GlobalTableConfig constructor.
	 *
	 * @param string|null $tablePrefix
	 */
	public function __construct(?string $tablePrefix = '')
	{
		$this->tablePrefix = $tablePrefix;
	}

	/**
	 * @return string
	 */
	public function getTablePrefix(): ?string
	{
		return $this->tablePrefix;
	}
}