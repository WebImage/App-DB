<?php

namespace WebImage\Db;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use WebImage\Db\Connection as WebImageConnection;

/**
 * Class QueryBuilder
 * @package WebImage\Db
 */
class QueryBuilder extends DoctrineQueryBuilder
{
	private ?ConnectionManager $connectionManager = null;
	private ?string $connectionName = null;
	/**
	 * QueryBuilder constructor.  Doctrine has decided to hide the Connection object, so we have to override the constructor
	 * @param Connection $connection
	 */
	public function __construct(Connection $connection)
	{
		parent::__construct($connection);
		if ($connection instanceof WebImageConnection) {
			$this->connectionManager = $connection->getConnectionManager();
			$this->connectionName = $connection->getConnectionName();
		}
	}

	/**
	 * Alters the table name to use, if necessary, based on Manager
	 * @param string|null $delete
	 * @param string|null $alias
	 *
	 * @return $this
	 */
	public function delete($delete = null, $alias = null): QueryBuilder
	{
		$delete = $this->getTableName($delete, ConnectionManager::MODE_WRITE);

		return parent::delete($delete, $alias);
	}

	/**
	 * Alters the table name to use, if necessary, based on Manager
	 *
	 * @param string|null $update
	 * @param string|null $alias
	 *
	 * @return $this
	 */
	public function update($update = null, $alias = null): QueryBuilder
	{
		$update = $this->getTableName($update, ConnectionManager::MODE_WRITE);

		return parent::update($update, $alias);
	}

	/**
	 * Alters the table name to use, if necessary, based on Manager
	 *
	 * @param string|null $insert
	 *
	 * @return $this
	 */
	public function insert($insert = null): QueryBuilder
	{
		$insert = $this->getTableName($insert, ConnectionManager::MODE_WRITE);

		return parent::insert($insert);
	}

	/**
	 * Alters the table name to use, if necessary, based on Manager
	 *
	 * @param string $from
	 * @param null $alias
	 *
	 * @return $this
	 */
	public function from($from, $alias = null): QueryBuilder
	{
		$from = $this->getTableName($from, ConnectionManager::MODE_READ);

		return parent::from($from, $alias);
	}

	/**
	 * Alters the table name to use, if necessary, based on Manager
	 *
	 * @param string $fromAlias
	 * @param string $join
	 * @param string $alias
	 * @param null $condition
	 *
	 * @return $this
	 */
	public function innerJoin($fromAlias, $join, $alias, $condition = null): QueryBuilder
	{
		$join = $this->getTableName($join, ConnectionManager::MODE_READ);

		return parent::innerJoin($fromAlias, $join, $alias, $condition);
	}

	/**
	 * Alters the table name to use, if necessary, based on Manager
	 *
	 * @param string $fromAlias
	 * @param string $join
	 * @param string $alias
	 * @param null $condition
	 *
	 * @return $this
	 */
	public function leftJoin($fromAlias, $join, $alias, $condition = null): QueryBuilder
	{
		$join = $this->getTableName($join, ConnectionManager::MODE_READ);

		return parent::leftJoin($fromAlias, $join, $alias, $condition);
	}

	/**
	 * Alters the table name to use, if necessary, based on Manager
	 *
	 * @param string $fromAlias
	 * @param string $join
	 * @param string $alias
	 * @param null $condition
	 *
	 * @return $this
	 */
	public function rightJoin($fromAlias, $join, $alias, $condition = null): QueryBuilder
	{
		$join = $this->getTableName($join, ConnectionManager::MODE_READ);

		return parent::rightJoin($fromAlias, $join, $alias, $condition);
	}

	/**
	 * @param string|null $tableKey
	 * @param string|null $mode Manager::MODE_READ | Manager::MODE_WRITE
	 *
	 * @return string
	 */
	protected function getTableName(string $tableKey = null, string $mode = null): ?string
	{
		if (null === $tableKey) return null;
		if (null !== $mode && !in_array($mode, [ConnectionManager::MODE_READ, ConnectionManager::MODE_WRITE])) {
			throw new \InvalidArgumentException('$mode should be Manager::MODE_READ or Manager::MODE_WRITE');
		}

		$table      = $tableKey;
//		$connection = $this->getConnection();

		if ($this->connectionManager !== null) {
//			$manager           = $this->connectionManager;
			$overrideTableName = $this->connectionManager->getTable($tableKey);

			if ($overrideTableName !== null) {
				$tableConnectionName = ($mode == ConnectionManager::MODE_WRITE) ? $overrideTableName->getWriteConnectionName() : $overrideTableName->getReadConnectionName();

				if (null !== $tableConnectionName && $tableConnectionName != $this->connectionName) {
					if ($this->connectionName !== null) {
						throw new MultiConnectionQueryBuilderException(sprintf('%s can only work with one connection at a time', __CLASS__));
					}
					// Overrides an already set connection
					$this->setConnection($this->connectionManager->getConnection($tableConnectionName));
				}
			}

			$table = $this->connectionManager->getTableName($tableKey);
		}

		return $table;
	}

	/**
	 * Overwrites underlying [private] collection property... what else can we do?
	 * @param Connection $connection
	 */
	private function setConnection(Connection $connection)
	{
		$p = new \ReflectionProperty(DoctrineQueryBuilder::class, 'connection');
		$p->setAccessible(true);
		$p->setValue($this, $connection);
		$p->setAccessible(false);
	}
}