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
use Joomla\Uri\Uri;
use Joomla\View\AbstractView;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Wilsonge\Api\Model\ItemModel;

abstract class AbstractUpdateController extends AbstractController implements LoggerAwareInterface, ContainerAwareInterface
{
	use ContainerAwareTrait, LoggerAwareTrait;

	/**
	 * The type of the documents being rendered
	 * 
	 * @var    string
	 * @since  1.0
	 */
	protected $type = '';

	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$id = $this->getInput()->getInt('id');

		if (!$id)
		{
			throw new \RuntimeException('No ID Supplied', 500);
		}

		/** @var AbstractDatabaseModel $model */
		if ($this->userModel)
		{
			$model = $this->initialiseModel($this->userModel);
		}
		else
		{
			$model = $this->initialiseModel($this->getControllerNameExtractor()->extract($this));
		}

		/** @var AbstractView $view */
		$view = $this->initialiseView();

		if (!$model instanceof ItemModel)
		{
			throw new \RuntimeException('Invalid model');
		}

		$newData = $this->getUpdateData();

		try
		{
			/** @var ItemModel $model */
			$model->updateItem($id, $newData);
		}
		catch (\Exception $e)
		{
			throw new \RuntimeException('Error updating the user', $e->getCode(), $e);
		}

		$item = $model->getItem($id);

		$data         = array();
		$data['id']   = $item['id'];
		$data['type'] = $this->type;
		$attributes = $this->addAttributes($item);
		$data['attributes'] = $attributes;

		$view->addData('data', $data);
		$view->addData('meta', array());

		$uri = new Uri($this->getApplication()->get('uri.request'));

		$links = [
			"self" => (string) $uri,
		];

		$view->addData('links', $links);

		$app = $this->getApplication();

		if ($app instanceof AbstractWebApplication)
		{
			$app->setBody($view->render());
		}
	}

	abstract public function addAttributes(array $item);

	abstract public function getUpdateData();
}
