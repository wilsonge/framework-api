<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api;

use Joomla\Application\AbstractWebApplication;
use Joomla\Controller\ControllerInterface;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;
use Joomla\Event\Event;
use Joomla\Event\EventImmutable;
use Joomla\Uri\Uri;

use Negotiation\Negotiator;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * Application class
 *
 * @since  1.0
 */
final class App extends AbstractWebApplication implements ContainerAwareInterface, DispatcherAwareInterface
{
	use ContainerAwareTrait, DispatcherAwareTrait;

	/**
	 * The Content Negotiation object
	 *
	 * @var   Negotiator|null
	 * @since 1.0
	 */
	protected $negotiator = null;

	/**
	 * Status codes translation table.
	 *
	 * The list of codes is complete according to the
	 * {@link http://www.iana.org/assignments/http-status-codes/ Hypertext Transfer Protocol (HTTP) Status Code Registry}
	 * (last updated 2015-05-19).
	 *
	 * Unless otherwise noted, the status code is defined in RFC2616.
	 *
	 * @var array
	 */
	public static $statusTexts = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',            // RFC2518
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',          // RFC4918
		208 => 'Already Reported',      // RFC5842
		226 => 'IM Used',               // RFC3229
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',    // RFC7238
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Payload Too Large',
		414 => 'URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',                                               // RFC2324
		422 => 'Unprocessable Entity',                                        // RFC4918
		423 => 'Locked',                                                      // RFC4918
		424 => 'Failed Dependency',                                           // RFC4918
		425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
		426 => 'Upgrade Required',                                            // RFC2817
		428 => 'Precondition Required',                                       // RFC6585
		429 => 'Too Many Requests',                                           // RFC6585
		431 => 'Request Header Fields Too Large',                             // RFC6585
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
		507 => 'Insufficient Storage',                                        // RFC4918
		508 => 'Loop Detected',                                               // RFC5842
		510 => 'Not Extended',                                                // RFC2774
		511 => 'Network Authentication Required',                             // RFC6585
	);

	/**
	 * Class constructor.
	 *
	 * @param   Container  $container  The container object
	 *
	 * @since   1.0
	 */
	public function __construct(Container $container)
	{
		$config = $container->get('config');

		// Run the parent constructor
		parent::__construct(null, $config);

		$container->set('App\\App', $this)
			->alias('Joomla\\Application\\AbstractWebApplication', 'App\\App')
			->alias('Joomla\\Application\\AbstractApplication', 'App\\App')
			->alias('app', 'App\\App')
			->set('Joomla\\Input\\Input', $this->input);

		$this->setContainer($container);
		$this->setLogger($container->get('Psr\\Log\\LoggerInterface'));
		$this->setDispatcher($container->get('Joomla\\Event\\Dispatcher'));
		$this->negotiator = new Negotiator;
	}

	/**
	 * Method to run the Web application routines.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function doExecute()
	{
		try
		{
			$routeCollection = $this->initialiseRoutes();
			$controller      = $this->route($routeCollection);

			$event = new Event('onBeforeExecuteRoute', array($controller));
			$this->getDispatcher()->triggerEvent($event);

			$controller->execute();

			$event = new EventImmutable('onAfterExecuteRoute', array($controller, $this));
			$this->getDispatcher()->triggerEvent($event);
		}
		catch (\Exception $e)
		{
			$code = $e->getCode();

			if (!isset(self::$statusTexts[$e->getCode()]))
			{
				$code = 500;
			}

			$reason = self::$statusTexts[$code];

			header('HTTP/1.1 ' . $code . ' ' . $reason, true, $code);

			$this->setBody($e->getMessage());
		}
	}

	/**
	 * Creates a list of potential routes
	 *
	 * @return RouteCollection
	 */
	protected function initialiseRoutes()
	{
		// Instantiate the router
		$routes = new RouteCollection();

		$swaggerFile = JPATH_CONFIGURATION . '/open-api.json';

		if (!file_exists($swaggerFile))
		{
			$this->getLogger()->critical('Open API routing file could not be found!');

			throw new \RuntimeException('Missing Open API File', 500);
		}

		// Now get the user management routes (these are from the generated swagger json file
		$swaggerJson = json_decode(file_get_contents($swaggerFile), true);

		if (!$swaggerJson)
		{
			$this->getLogger()->critical('Open API routing file could not be found!');

			throw new \RuntimeException('Invalid Open API File', 500);
		}

		foreach ($swaggerJson['paths'] as $url => $operations)
		{
			foreach ($operations as $httpMethod => $operation)
			{
				$uniqueName = $url . strtoupper($httpMethod);

				$routes->add(
					$uniqueName,
					new Route(
						$swaggerJson['basePath'] . $url,
						array(
							'controller' => $operation['operationId']
						),
						array(),
						array(),
						'',
						array(),
						array(strtoupper($httpMethod))
					)
				);
			}
		}

		return $routes;
	}

	/**
	 * Maps the route to a controller
	 *
	 * @param   RouteCollection  $routeCollection  The potential routes
	 *
	 * @return  ControllerInterface
	 */
	protected function route($routeCollection)
	{
		$uri = new Uri($this->get('uri.request'));
		$context = new RequestContext(
			$uri->toString(),
			$this->input->getMethod(),
			$uri->toString(array('scheme', 'user', 'pass', 'host', 'port')),
			$this->isSslConnection() ? 'https://' : 'http://',
			80,
			443,
			$uri->toString(array('path')),
			ltrim($uri->toString(array('query')), '?')
		);

		$matcher = new UrlMatcher($routeCollection, $context);

		// Parse the route
		try
		{
			$path = $uri->toString(array('path'));

			if ($path !== '/' && rtrim($path, '/') !== $path)
			{
				$path = rtrim($path, '/');
			}

			$routerResult = $matcher->match($path);
		}
		catch (ResourceNotFoundException $e)
		{
			throw new \InvalidArgumentException(self::$statusTexts[404], 404, $e);
		}
		catch (MethodNotAllowedException $e)
		{
			$this->getLogger()->warning(
				sprintf(
					'The user tried to use method %s to access the URL %s',
					$this->input->getMethod(),
					$uri->toString()
				),
				array('exception' => $e)
			);

			throw new \InvalidArgumentException(self::$statusTexts[405], 405, $e);
		}
		catch (\Exception $e)
		{
			$this->getLogger()->warning('There was an error in the router', array('exception' => $e));

			throw new \InvalidArgumentException(self::$statusTexts[500], 500, $e);
		}

		// Check the accept header matches the expected format for this match
		$acceptHeader = $this->input->server->getString('HTTP_ACCEPT', null);

		if ($acceptHeader)
		{
			// TODO: Get these priorities from the open api file
			$priorities   = array('application/json');

			$mediaType = $this->negotiator->getBest($acceptHeader, $priorities);

			// If we have a null media type then we couldn't find a matching content type - so the correct result is a 404
			if (is_null($mediaType))
			{
				$this->getLogger()->warning(
					sprintf(
						'User supplied accept header of %s, but endpoint only accepts %s',
						$acceptHeader,
						implode(',', $priorities)
					)
				);

				throw new \InvalidArgumentException(self::$statusTexts[404], 404);
			}

			/** @var \Negotiation\BaseAccept $mediaType */
			$value = $mediaType->getValue();
		}

		// Retrieve the controller path. Try and assemble it into a namespace class to search
		$controllerPath = $routerResult['controller'];
		$controllerPieces = explode('.', $controllerPath);

		foreach ($controllerPieces as &$controllerPiece)
		{
			$controllerPiece = ucfirst(strtolower($controllerPiece));
		}

		$controllerClassName = implode('\\', $controllerPieces);

		if (!class_exists($controllerClassName))
		{
			$this->getLogger()->critical('The controller cannot be found!');

			throw new \InvalidArgumentException('Endpoint not found', 500);
		}

		if (!is_subclass_of($controllerClassName, '\\Joomla\\Controller\\AbstractController'))
		{
			$this->getLogger()->warning('The controller is not a Joomla Abstract Controller instance!');

			throw new \InvalidArgumentException('Endpoint not found', 500);
		}

		/**
		 * We only set the controller variable so we knew which controller to boot. We don't want this set into the
		 * input variable
		 */
		unset($routerResult['controller']);

		// Set any remaining variables from the routing into the input object
		foreach($routerResult as $variableName => $routeValue)
		{
			$this->input->set($variableName, $routeValue);
		}

		// Build the controller instance in the DI Container
		$controller = $this->container->buildObject($controllerClassName);

		// If the controller is container aware set the container
		if ($controller instanceof ContainerAwareInterface)
		{
			$controller->setContainer($this->getContainer());
		}

		// If the controller is logger aware set the logger.
		if ($controller instanceof LoggerAwareInterface)
		{
			$controller->setLogger($this->getLogger());
		}

		// If the controller is event aware (e.g. because it uses a command bus) set the dispatcher.
		if ($controller instanceof DispatcherAwareInterface)
		{
			$controller->setDispatcher($this->getDispatcher());
		}

		$this->getLogger()->debug(sprintf('Controller %s was successfully initialised', get_class($controller)));

		return $controller;
	}
}
