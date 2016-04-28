<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Service;

use Joomla\Database\DatabaseFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;

/**
 * Database service provider.
 *
 * @since  1.0
 */
class DatabaseProvider implements ServiceProviderInterface
{
	/**
	 * Configuration object.
	 *
	 * @var    Registry
	 *
	 * @since  1.0
	 */
	protected $config;

	/**
	 * {@inheritdoc}
	 */
	public function register(Container $container)
	{
		$container->share(
			'Joomla\\Database\\DatabaseDriver',
			function () use ($container)
			{
				$config = $container->get('config');

				$factory = new DatabaseFactory;
				return $factory->getDriver(
					$config->get('database.driver'),
					array(
						'host'     => $config->get('database.host'),
						'user'     => $config->get('database.user'),
						'password' => $config->get('database.password'),
						'database' => $config->get('database.name')
					)
				);
			}
		);

		$container->alias('db', 'Joomla\\Database\\DatabaseDriver');
	}
}
