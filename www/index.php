<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// Set the default timezone in it hasn't been set on the server
date_default_timezone_set('UTC');

// Define required paths
define('JPATH_ROOT',          dirname(__DIR__));
define('JPATH_CONFIGURATION', JPATH_ROOT . '/src/Config');

// Load the Composer autoloader
require JPATH_ROOT . '/vendor/autoload.php';

$container = new \Joomla\DI\Container;
$container->registerServiceProvider(new \Wilsonge\Api\Service\ConfigProvider(JPATH_CONFIGURATION . '/config.json'))
	->registerServiceProvider(new \Wilsonge\Api\Service\LoggingProvider)
	->registerServiceProvider(new \Wilsonge\Api\Service\DatabaseProvider)
	->registerServiceProvider(new \Wilsonge\Api\Service\EventProvider);

// Instantiate the application.
$application = new \Wilsonge\Api\App($container);

// Execute the application.
$application->execute();
