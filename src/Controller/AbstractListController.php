<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller;

use Joomla\Application\AbstractWebApplication;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Model\AbstractDatabaseModel;
use Joomla\Model\ModelInterface;
use Joomla\View\AbstractView;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Wilsonge\Api\Model\ListModel;

abstract class AbstractListController extends AbstractController implements LoggerAwareInterface, ContainerAwareInterface
{
	use ContainerAwareTrait, LoggerAwareTrait;

	/**
	 * The type of the documents being rendered
	 * 
	 * @var    string
	 * @since  1.0
	 */
	protected $type = '';

	public function execute()
	{
		$page = $this->getInput()->getInt('page');
		$size = $this->getInput()->getInt('size');

		/** @var AbstractDatabaseModel $model */
		$model = $this->initialiseModel($this->getControllerNameExtractor()->extract($this));

		/** @var AbstractView $view */
		$view = $this->initialiseView();

		if (!$model instanceof ListModel)
		{
			throw new \RuntimeException('Invalid model');
		}

		/** @var ListModel $model */
		$items = $model->getItems($page, $size);
		$jsonItems = array();

		foreach ($items as $item)
		{
			$data         = array();
			$data['id']   = $item['id'];
			$data['type'] = $this->type;
			$attributes   = array();
			$this->addAttributes($item, $attributes);
			$data['attributes'] = $attributes;
			$jsonItems[] = $data;
		}

		$view->addData('data', $jsonItems);

		$total = $model->getTotal();
		$pages = ceil($total % $size);
		$view->addData('meta', array('total-pages' => $pages));

		$app = $this->getApplication();

		if ($app instanceof AbstractWebApplication)
		{
			$app->setBody($view->render());
		}
	}

	abstract public function addAttributes(array $item, array $attributes);
}
