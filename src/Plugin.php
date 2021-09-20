<?php

namespace WebImage\Db;

use League\Container\Definition\ClassDefinition;
use WebImage\Application\AbstractPlugin;
use WebImage\Application\ApplicationInterface;

class Plugin extends AbstractPlugin
{
	public function load(ApplicationInterface $app)
	{
		parent::load($app);

		$app->getServiceManager()->addServiceProvider(new DatabaseServiceProvider);
	}
}