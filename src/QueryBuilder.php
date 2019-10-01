<?php

namespace WebImage\Db;

use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use Doctrine\DBAL\Connection;

/**
 * Class QueryBuilder
 * @package WebImage\Db
 */
class QueryBuilder extends DoctrineQueryBuilder
{

	/** @var ConnectionManager */
	private $manager;
	private $connectionName;
	/**
	 * QueryBuilder constructor.
	 *
	 * @param ConnectionManager $manager
	 * @param string|null $connectionName
	 */
	public function __construct(ConnectionManager $manager, $connectionName=null)
	{
		parent::__construct($manager->getConnection($connectionName));
		$this->manager = $manager;
		$this->connectionName = $connectionName;
	}

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
	 * @return ConnectionManager
	 */
	public function getManager()/*: Manager */
	{
		return $this->manager;
	}

	/**
	 * @param string|null $tableKey
	 * @param string $mode Manager::MODE_READ | Manager::MODE_WRITE
	 *
	 *
	 *
	 * @return string
	 */
	protected function getTableName($tableKey = null, $mode)
	{
		if (null === $tableKey) return;
		if (null !== $mode && !in_array($mode, [ConnectionManager::MODE_READ, ConnectionManager::MODE_WRITE])) {
			throw new \InvalidArgumentException('$mode should be Manager::MODE_READ or Manager::MODE_WRITE');
		}

		$table = $this->getManager()->getTable($tableKey);

		if (null !== $table) {
			$tableConnectionName = ($mode == ConnectionManager::MODE_WRITE) ? $table->getWriteConnectionName() : $table->getReadConnectionName();

			if (null !== $tableConnectionName && $tableConnectionName != $this->connectionName) {
				if ($this->connectionName != null) {
					throw new MultiConnectionQueryBuilderException(sprintf('%s can only work with one connection at a time', __CLASS__));
				}
				// Overrides an already set connection
				$this->setConnection($this->getManager()->getConnection($tableConnectionName));
			}
		}

		return $this->getManager()->getTableName($tableKey);
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