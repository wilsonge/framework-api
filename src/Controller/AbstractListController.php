<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller;

use Joomla\Application\AbstractWebApplication;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Filter\InputFilter;
use Joomla\Model\AbstractDatabaseModel;
use Joomla\Uri\Uri;
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

	/**
	 * The default number of items to load
	 * 
	 * @var    int
	 * @since  1.0
	 */
	protected $defaultNumber = 50;

	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$page = $this->getInput()->get('page', array(), 'array');
		$inputFilter = new InputFilter;

		if (array_key_exists('number', $page))
		{
			$pageNumber = $inputFilter->clean($page['number'], 'INT');
		}
		else
		{
			$pageNumber = 1;
		}

		if (array_key_exists('size', $page))
		{
			$size = $inputFilter->clean($page['size'], 'INT');
		}
		else
		{
			$size = $this->defaultNumber;
		}

		/** @var AbstractDatabaseModel $model */
		$model = $this->initialiseModel($this->getControllerNameExtractor()->extract($this));

		/** @var AbstractView $view */
		$view = $this->initialiseView();

		if (!$model instanceof ListModel)
		{
			throw new \RuntimeException('Invalid model');
		}

		/** @var ListModel $model */
		$items = $model->getItems($pageNumber - 1, $size);
		$jsonItems = array();

		foreach ($items as $item)
		{
			$data         = array();
			$data['id']   = $item['id'];
			$data['type'] = $this->type;
			$attributes = $this->addAttributes($item);
			$data['attributes'] = $attributes;
			$jsonItems[] = $data;
		}

		$view->addData('data', $jsonItems);

		$total = $model->getTotal();
		$totalPages = ceil($total / $size);
		$view->addData('meta', array('total-pages' => $totalPages));

		$uri = new Uri($this->getApplication()->get('uri.request'));
		$firstUri = clone $uri;
		$firstUri->setVar('page', 1);

		// If we are on the first page set the previous link to null
		if ($pageNumber == 1)
		{
			$prevUri = null;
		}
		else
		{
			$prevUri = clone $uri;
			$prevUri->setVar('page', $pageNumber - 1);
		}

		// If we are on the last page set the next link to null
		if ($pageNumber == $totalPages)
		{
			$nextUri = null;
		}
		else
		{
			$nextUri = clone $uri;
			$nextUri->setVar('page', $pageNumber + 1);
		}

		$lastUri = clone $uri;
		$lastUri->setVar('page', $totalPages);

		$links = [
			"self" => (string) $uri,
			"first" => (string) $firstUri,
			"prev" => (string) $prevUri,
			"next" => (string) $nextUri,
			"last" => (string) $lastUri,
		];

		$view->addData('links', $links);

		$app = $this->getApplication();

		if ($app instanceof AbstractWebApplication)
		{
			$app->setBody($view->render());
		}
	}

	abstract public function addAttributes(array $item);
}
