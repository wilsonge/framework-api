<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller\ClassExtractor;

use Wilsonge\Api\Exception\CanNotDetermineControllerNameException;

/**
 * Extract the name from the class
 */
class ClassNameExtractor implements ControllerNameExtractor
{
    /**
     * Extract the name from a command
     *
     * @param   object  $controller  The Controller name to extract
     *
     * @return  string
     *
     * @throws  CannotDetermineControllerNameException
     */
    public function extract($controller)
    {
        $controllerName = get_class($controller);

        if (strpos($controllerName, '\\') !== false)
        {
            $controllerName = substr($controllerName, strrpos($controllerName, '\\') + 1);
        }

        return $controllerName;
    }
}
