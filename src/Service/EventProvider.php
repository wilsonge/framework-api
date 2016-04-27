<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\Dispatcher;

/**
 * Logging service provider.
 *
 * @since  1.0
 */
class EventProvider implements ServiceProviderInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function register(Container $container)
	{
		$container->share(
			'Joomla\\Event\\Dispatcher',
			function ()
			{
				$dispatcher = new Dispatcher;

				return $dispatcher;
			}
		);
	}
}
