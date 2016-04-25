<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller\Pets;

use Joomla\Controller\AbstractController;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class PetList extends AbstractController implements LoggerAwareInterface, ContainerAwareInterface
{
    use ContainerAwareTrait, LoggerAwareTrait;

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}