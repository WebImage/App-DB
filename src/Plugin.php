<?php

namespace WebImage\Db;

use WebImage\Application\AbstractPlugin;
use WebImage\Application\ApplicationInterface;

class Plugin extends AbstractPlugin
{
	protected function load(ApplicationInterface $app): void
	{
		$app->getServiceManager()->addServiceProvider(new DatabaseServiceProvider);
	}
}