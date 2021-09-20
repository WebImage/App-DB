<?php

namespace WebImage\Db;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;

/**
 * Class QueryBuilder
 * @package WebImage\Db
 */
class QueryBuilder extends DoctrineQueryBuilder
{
	/**
	 * Alters the table name to use, if necessary, based on Manager
	 * @param string|null $delete
	 * @param string|null $alias
	 *
	 * @return $this
	 */
	public function delete($delete = null, $alias = null)
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
	public function update($update = null, $alias = null)
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
	public function insert($insert = null)
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
	public function from($from, $alias = null)
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
	public function innerJoin($fromAlias, $join, $alias, $condition = null)
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
	public function leftJoin($fromAlias, $join, $alias, $condition = null)
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
	public function rightJoin($fromAlias, $join, $alias, $condition = null)
	{
		$join = $this->getTableName($join, ConnectionManager::MODE_READ);

		return parent::rightJoin($fromAlias, $join, $alias, $condition);
	}

	/**
	 * @param string|null $tableKey
	 * @param string $mode Manager::MODE_READ | Manager::MODE_WRITE
	 *
	 * @return string
	 */
	protected function getTableName($tableKey = null, $mode): ?string
	{
		if (null === $tableKey) return null;
		if (null !== $mode && !in_array($mode, [ConnectionManager::MODE_READ, ConnectionManager::MODE_WRITE])) {
			throw new \InvalidArgumentException('$mode should be Manager::MODE_READ or Manager::MODE_WRITE');
		}

		$table      = $tableKey;
		$connection = $this->getConnection();

		if ($connection instanceof \WebImage\Db\Connection) {
			$manager           = $connection->getConnectionManager();
			$overrideTableName = $manager->getTable($tableKey);

			if ($overrideTableName !== null) {
				$tableConnectionName = ($mode == ConnectionManager::MODE_WRITE) ? $overrideTableName->getWriteConnectionName() : $overrideTableName->getReadConnectionName();

				if (null !== $tableConnectionName && $tableConnectionName != $connection->getConnectionName()) {
					if ($connection->getConnectionName() !== null) {
						throw new MultiConnectionQueryBuilderException(sprintf('%s can only work with one connection at a time', __CLASS__));
					}
					// Overrides an already set connection
					$this->setConnection($manager->getConnection($tableConnectionName));
				}
			}

			$table = $manager->getTableName($tableKey);
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