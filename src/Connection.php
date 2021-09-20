<?php

namespace WebImage\Db;

class Connection extends \Doctrine\DBAL\Connection
{
	/** @var string */
	private $connectionName;
	/** @var ConnectionManager */
	private $connectionManager;

	public function createQueryBuilder()
	{
		return new QueryBuilder($this);
	}

	/**
	 * @return string
	 */
	public function getConnectionName(): string
	{
		return $this->connectionName;
	}

	/**
	 * @param string $connectionName
	 */
	public function setConnectionName(string $connectionName): void
	{
		$this->connectionName = $connectionName;
	}

	/**
	 * @return ConnectionManager
	 */
	public function getConnectionManager(): ConnectionManager
	{
		return $this->connectionManager;
	}

	/**
	 * @param ConnectionManager $connectionManager
	 */
	public function setConnectionManager(ConnectionManager $connectionManager): void
	{
		$this->connectionManager = $connectionManager;
	}
}