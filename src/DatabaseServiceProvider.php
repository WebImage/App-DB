<?php

namespace WebImage\Db;

use RuntimeException;
use WebImage\Application\ApplicationInterface;
use WebImage\Config\Config;
use WebImage\Container\ServiceProvider\AbstractServiceProvider;

class DatabaseServiceProvider extends AbstractServiceProvider
{
	const CONFIG_DATABASE = 'database';
	const CONFIG_CONNECTIONS = 'connections';
	const CONFIG_SETTINGS = 'settings'; // tablePrefix
	const CONFIG_TABLES = 'tables';

	protected array $provides = [
		ConnectionManager::class
	];

	public function register(): void
	{
		$config = $this->getConfig();
		$this->registerManager($config);
	}

	private function registerManager(Config $config)
	{
		$this->getContainer()->addShared(ConnectionManager::class, function() use ($config) {

			$managerConfig = $this->getManagerConfig($config);

			$manager = new ConnectionManager($managerConfig);
			$this->setupConnections($manager, $config);
			$this->setupTables($manager, $config);

			return $manager;
		});
	}

	/**
	 * Add database connection settings to Manager
	 *
	 * @param ConnectionManager $manager
	 * @param Config $config
	 */
	private function setupConnections(ConnectionManager $manager, Config $config)
	{
		$connections = isset($config[self::CONFIG_CONNECTIONS]) ? $config[self::CONFIG_CONNECTIONS] : new Config();
		foreach($connections as $name => $params) {
			$manager->setConnectionParams($name, new ConnectionParams($name, $params));
		}
	}

	/**
	 * Add table configuration settings to Manager
	 *
	 * @param ConnectionManager $manager
	 * @param Config $config
	 */
	private function setupTables(ConnectionManager $manager, Config $config)
	{
		$tables = isset($config[self::CONFIG_TABLES]) ? $config[self::CONFIG_TABLES] : new Config();
		/**
		 * @var string $tableKey
		 * @var Config|string $settings
		 */
		foreach($tables as $tableKey => $settings) {
			$tableName = null;

			$tableConfig = null;

			if (is_string($settings)) {
				$tableConfig = new TableConfig($tableKey, $tableName = $settings);
			} else if ($settings instanceof Config) {

				$tableName = $settings->get('table');
				$readConnectionName = $settings->get('readConnection', $settings->get('connection'));
				$writeConnectionName = $settings->get('writeConnection' ,$settings->get('connection'));
				$useGlobalPrefix = $settings->get('useGlobalPrefix', true);
				$tableConfig = new TableConfig($tableKey, $tableName, $readConnectionName, $writeConnectionName, $useGlobalPrefix);
			} else {
				throw new RuntimeException('Invalid table configuration');
			}

			$manager->addTable($tableKey, $tableConfig);
		}
	}

	/**
	 * Add global database settings to Manager
	 *
	 * @param Config $config
	 *
	 * @return ManagerConfig
	 */
	private function getManagerConfig(Config $config): ManagerConfig
	{
		$config = isset($config[self::CONFIG_SETTINGS]) ? $config[self::CONFIG_SETTINGS] : new Config();
		$tablePrefix = $config->get('tablePrefix', '');

		return new ManagerConfig($tablePrefix);
	}

	/**
	 * Get the configuration related to database connects
	 */
	private function getConfig()
	{
		/** @var ApplicationInterface $app */
		$app = $this->getContainer()->get(ApplicationInterface::class);
		$config = $app->getConfig();
		$databaseConfig = isset($config[self::CONFIG_DATABASE]) ? $config[self::CONFIG_DATABASE] : new Config();

		return $databaseConfig;
	}
}