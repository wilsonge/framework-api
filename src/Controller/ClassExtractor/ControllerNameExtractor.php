<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller\ClassExtractor;

use Wilsonge\Api\Exception\CanNotDetermineControllerNameException;

/**
 * Extract the name from a controller so that the name can be determined
 * by the context better than simply the class name
 */
interface ControllerNameExtractor
{
    /**
     * Extract the name from a command
     *
     * @param   object  $command  The Controller name to extract
     *
     * @return  string
     *
     * @throws  CannotDetermineControllerNameException
     */
    public function extract($command);
}
