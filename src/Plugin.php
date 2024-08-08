<?php

namespace WebImage\Db;

use WebImage\Application\AbstractPlugin;
use WebImage\Application\ApplicationInterface;

class Plugin extends AbstractPlugin
{
	public function load(ApplicationInterface $app): void
	{
		parent::load($app);

		$app->getServiceManager()->addServiceProvider(new DatabaseServiceProvider);
	}
}