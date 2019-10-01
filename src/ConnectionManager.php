<?php

namespace WebImage\Db;

use RuntimeException;
use WebImage\Core\Dictionary;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionManager
{
	const DEFAULT_CONNECTION = 'default';
	const MODE_ALL = 'both';
	const MODE_READ = 'read';
	const MODE_WRITE = 'write';
	/** @var Dictionary */
	private $connections;
	/** @var Dictionary */
	private $connectionParams;
	/** @var Dictionary */
	private $tables;
	/** @var ManagerConfig */
	private $config;
	/**
	 * Manager constructor.
	 * @param ManagerConfig $config
	 */
	public function __construct(ManagerConfig $config)
	{
		$this->config = $config;
		$this->connections = new Dictionary();
		$this->connectionParams = new Dictionary();
		$this->tables = new Dictionary();
	}

	/**
	 * @return ManagerConfig
	 */
	public function getConfig(): ManagerConfig
	{
		return $this->config;
	}
	/**
	 * Establish a database connections via Doctrine
	 * @param string|null $name
	 * @param string|null $mode Either self::MODE_READ or self::MODE_WRITE
	 *
	 * @return Connection
	 */
	public function getConnection($name=null, $mode=null)
	{
		if (null === $name) $name = self::DEFAULT_CONNECTION;
		if (null !== $mode && $mode != self::MODE_READ && $mode != self::MODE_WRITE) {
			throw new RuntimeException(sprintf('Expecting %s or %s for mode', self::MODE_READ, self::MODE_WRITE));
		}

		$params = $this->getConnectionParams($name);

		$connection_params = $this->getParamsForMode($params, $mode);

		$key = $name;

		if ($mode == self::MODE_READ && $params->hasReadSettings()) {
			$key .= '.' . self::MODE_READ;
		} else if ($mode == self::MODE_WRITE && $params->hasWriteSettings()) {
			$key .= '.' . self::MODE_WRITE;
		}

		if (!$this->connections->has($key)) {
			$this->connections->set(
				$key,
				DriverManager::getConnection($connection_params)
			);
		}

		return $this->connections->get($key);
	}

	/**
	 * Gets the connection parameters for a given $connectionName
	 *
	 * @throws RuntimeException
	 *
	 * @return ConnectionParams
	 */
	public function getConnectionParams($connectionName)
	{
		if (!$this->connectionParams->has($connectionName)) {
			throw new RuntimeException(sprintf('Unknown connection name: %s', $connectionName));
		}

		return $this->connectionParams->get($connectionName);
	}

	/**
	 * @param string $connectionName The name of the connection
	 * @param ConnectionParams $params
	 */
	public function setConnectionParams($connectionName, ConnectionParams $params)
	{
		$this->connectionParams->set($connectionName, $params);
	}

	/**
	 * Create a query builder
	 *
	 * param string $connectionName
	 *
	 * @return QueryBuilder
	 */
	public function createQueryBuilder(/*$connectionName: string */)/*: QueryBuilder */
	{
		return new QueryBuilder($this);
	}

	/**
	 * Retrieve the database table name to use
	 *
	 * @param string $tableKey
	 *
	 * @return string
	 */
	public function getTableName($tableKey)
	{
		$table = $this->getTable($tableKey);
		$prefix = $this->getConfig()->getTablePrefix();
		$usePrefix = true;

		// Default table name to table key
		$tableName = $tableKey;

		if (null !== $table) {
			// Override tableName if overridden in table config

			if (null !== $table->getTableName()) {
				$tableName = $table->getTableName();
			}
			if (!$table->useGlobalPrefix()) $usePrefix = false;
		}

		if ($usePrefix) $tableName = $prefix . $tableName;

		return $tableName;
	}

	/**
	 * @param $tableKey
	 *
	 * @return TableConfig|null
	 */
	public function getTable($tableKey)
	{
		return $this->tables->get($tableKey);
	}

	/**
	 * @param $tableKey
	 * @param TableConfig $config
	 */
	public function setTable($tableKey, TableConfig $config)
	{
		$this->tables->set($tableKey, $config);
	}

	/**
	 * Add configuration settings for a table.  Ensures that table config has not already been added.
	 *
	 * @param $tableKey
	 * @param TableConfig $config
	 */
	public function addTable($tableKey, TableConfig $config)
	{
		if ($this->tables->has($tableKey)) {
			throw new RuntimeException(sprintf('The table configuration for %s has already been added', $tableKey));
		}
		$this->setTable($tableKey, $config);
	}

	/**
	 * @param ConnectionParams|null $params
	 * @param string|null $mode MODE_READ or MODE_WRITE
	 *
	 * @return array
	 */
	private function getParamsForMode(ConnectionParams $params, $mode=null)
	{
		$p = null;
		if ($mode == self::MODE_READ) {
			$p = $params->forReading();
		} else if ($mode == self::MODE_WRITE) {
			$p = $params->forWriting();
		} else {
			$p = $params->forDefault();
		}

		return $p;
	}
}