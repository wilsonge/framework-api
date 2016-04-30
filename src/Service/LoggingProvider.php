<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Logging service provider.
 *
 * @since  1.0
 */
class LoggingProvider implements ServiceProviderInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function register(Container $container)
	{
		$container->share(
			'Mongolog\\Logger',
			function ()
			{
				$log = new Logger('application');
				$log->pushProcessor(new IntrospectionProcessor);
				$log->pushProcessor(new ProcessIdProcessor);

				// Initialise the syslog handler and formatter and inject it into the logger
				$sysLog = new StreamHandler(JPATH_ROOT . '/debug.log');
				$formatter = new LineFormatter("%datetime% %level_name% %extra.file%:%extra.line% %extra.class%: %message% %context%\n", 'Y-m-d H:i:s,u');
				$formatter->includeStacktraces(true);
				$formatter->ignoreEmptyContextAndExtra(true);
				$sysLog->setFormatter($formatter);
				$log->pushHandler($sysLog);

				\Monolog\ErrorHandler::register($log);

				return $log;
			}
		);

		// Alias the monolog to the PSR interface for future interoperability
		$container->alias('Psr\\Log\\LoggerInterface', 'Mongolog\\Logger');
	}
}
