<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller\Users;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Model\AbstractDatabaseModel;
use Joomla\View\AbstractView;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Wilsonge\Api\Controller\AbstractController;

class Users extends AbstractController implements LoggerAwareInterface, ContainerAwareInterface
{
	use ContainerAwareTrait, LoggerAwareTrait;

	public function execute()
	{
		$page = $this->getInput()->get('page');
		$size = $this->getInput()->get('size');

		/** @var AbstractDatabaseModel $model */
		$model = $this->initialiseModel($this->getControllerNameExtractor()->extract($this));

		/** @var AbstractView $view */
		$view = $this->initialiseView();
	}
}
