<?php

namespace WebImage\Db;

use WebImage\Config\Config;

class ConnectionParams
{
	const KEY_READ = 'read';
	const KEY_WRITE = 'write';

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * ConnectionParams constructor.
	 *
	 * @param Config $config
	 */
	public function __construct($name, Config $config)
	{
		$this->name = $name;
		$this->config = $config;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Retrieve read-related settings
	 *
	 * @return array
	 */
	public function forReading(): array
	{
		return $this->mergedSettings($this->config->toArray(), self::KEY_READ);
	}

	/**
	 * Retrieve write-related settings
	 *
	 * @return array
	 */
	public function forWriting(): array
	{
		return $this->mergedSettings($this->config->toArray(), self::KEY_WRITE);
	}

	/**
	 * All settings converted to array
	 *
	 * @return array
	 */
	public function forDefault() :array
	{
		return $this->mergedSettings($this->config->toArray());
	}

	/**
	 * Merges a sub-keys values into the main block
	 *
	 * @param array $array
	 * @param null $mergeKey
	 *
	 * @return array
	 */
	private function mergedSettings(array $array, $mergeKey=null): array
	{
		$settings = $this->config->toArray();

		if (isset($settings[$mergeKey])) {
			$settings = array_merge($settings, $settings[$mergeKey]);
		}

		unset($settings[self::KEY_READ]);
		unset($settings[self::KEY_WRITE]);

		return $settings;
	}

	/**
	 * Check if separate read settings are defined
	 *
	 * @return bool
	 */
	public function hasReadSettings(): bool
	{
		return isset($this->config[self::KEY_READ]);
	}

	/**
	 * Check if separate write settings are defined
	 *
	 * @return bool
	 */
	public function hasWriteSettings(): bool
	{
		return isset($this->config[self::KEY_WRITE]);
	}
}