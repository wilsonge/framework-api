<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Exception;

use Joomla\Controller\AbstractController;

/**
 * Thrown when a ControllerNameExtractor cannot determine the command's name
 */
class CanNotDetermineControllerNameException extends \RuntimeException
{
	/**
	 * @var  AbstractController
	 */
	private $command;

	/**
     * Generate a exception for a given controller
     *
	 * @param   AbstractController  $controller  
	 *
	 * @return  static
	 */
	public static function forCommand(AbstractController $controller)
	{
		$type =  is_object($controller) ? get_class($controller) : gettype($controller);
		$exception = new static('Could not determine command name of ' . $type);
		$exception->command = $controller;

		return $exception;
	}

	/**
	 * Returns the command that could not be invoked
	 *
	 * @return mixed
	 */
	public function getCommand()
	{
		return $this->command;
	}
}
