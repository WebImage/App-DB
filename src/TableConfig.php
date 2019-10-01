<?php

namespace WebImage\Db;

class TableConfig
{
	/** @var string The keyed table value that will be translated to $tableName */
	private $tableKey;
	/** @var string The actual table name to use */
	private $tableName;
	/** @var string The read connection to use for performing read operations */
	private $readConnectionName;
	/** @var string The write connection to use for performing write operations */
	private $writeConnectionName;
	/** @var bool */
	private $useGlobalPrefix;

	/**
	 * TableConfig constructor.
	 *
	 * @param string $tableKey
	 * @param string $tableName
	 * @param string $readConnectionName
	 * @param string $writeConnectionName
	 * @param bool $useGlobalPrefix
	 */
	public function __construct($tableKey, $tableName, $readConnectionName = null, $writeConnectionName = null, $useGlobalPrefix = true)
	{
		$this->tableKey = $tableKey;
		$this->tableName = $tableName;
		$this->readConnectionName = $readConnectionName;
		$this->writeConnectionName = $writeConnectionName;
		$this->useGlobalPrefix = $useGlobalPrefix;

		if (!is_bool($useGlobalPrefix)) {
			throw new \InvalidArgumentException(sprintf('%s was expecting a boolean value for $useGlobalPrefix', __METHOD__));
		}
	}

	/**
	 * @return string
	 */
	public function getTableKey()/* : string */
	{
		return $this->tableKey;
	}

	/**
	 * @return string
	 */
	public function getTableName()/* : string */
	{
		return $this->tableName;
	}

	/**
	 * @return string
	 */
	public function getReadConnectionName()/* : string */
	{
		return $this->readConnectionName;
	}

	/**
	 * @return string
	 */
	public function getWriteConnectionName()/* : string */
	{
		return $this->writeConnectionName;
	}

	/**
	 * @return bool
	 */
	public function useGlobalPrefix()/* : bool */
	{
		return $this->useGlobalPrefix;
	}
}