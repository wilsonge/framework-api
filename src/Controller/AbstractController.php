<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller;

use Joomla\Controller\AbstractController as AbstractJoomlaController;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Wilsonge\Api\Controller\ClassExtractor\ClassNameExtractor;
use Wilsonge\Api\Controller\ClassExtractor\ControllerNameExtractor;

abstract class AbstractController extends AbstractJoomlaController implements LoggerAwareInterface, ContainerAwareInterface
{
    use ContainerAwareTrait, LoggerAwareTrait;

    /**
     * The controller name extractor
     * 
     * @var  ControllerNameExtractor
     */
    protected $controllerNameExtractor;

    /**
     * Method to initialise the model object
     *
     * @param   string  $mName  The name of the model to initialise (usually the same as the view)
     *
     * @return  \Joomla\Model\ModelInterface
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function initialiseModel($mName)
    {
        $model = '\\Wilsonge\\Api\\Model\\' . ucfirst(strtolower($mName)) . 'Model';

        // If there isn't a class, panic.
        if (!class_exists($model))
        {
            throw new \RuntimeException(sprintf('No model found for view %s', $mName));
        }

        $object = $this->getContainer()->buildObject($model);
        $this->getContainer()->alias('Joomla\\Model\\ModelInterface', $model);

        return $object;
    }

    /**
     * Method to initialize the view object
     *
     * @return  \Joomla\View\ViewInterface  View object
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function initialiseView()
    {
        $view   = ucfirst(strtolower($this->getControllerNameExtractor()->extract($this)));
        $format = ucfirst($this->getInput()->getWord('format', 'html'));

        // See if we have already initialised the model - if we haven't then initialise it
        try
        {
            $this->getContainer()->get('Joomla\\Model\\ModelInterface');
        }
        catch (\InvalidArgumentException $e)
        {
            $this->initialiseModel($view);
        }

        $class = '\\Wilsonge\\Api\\View\\' . $format . '\\' . $view . '\\' . $view . $format . 'View';

        // If an extension default view doesn't exist, fall back to the default application view
        if (!class_exists($class))
        {
            $class = '\\Wilsonge\\Api\\View\\' . $format . '\\Default' . $format . 'View';

            // If we still have nothing, maybe Joomla core has an option
            if (!class_exists($class))
            {
                $class = '\\Joomla\\View\\Base' . $format . 'View';

                // Still nothing?  Well, with this many options, we can say we did our best.
                if (!class_exists($class))
                {
                    throw new \RuntimeException(
                        sprintf('A view class was not found for the %s view in the %s format.', $view, $format)
                    );
                }
            }
        }

        return $this->getContainer()->buildObject($class);
    }

    protected function setControllerNameExtractor(ControllerNameExtractor $extractor)
    {
        $this->controllerNameExtractor = $extractor;
    }

    protected function getControllerNameExtractor()
    {
        if ($this->controllerNameExtractor)
        {
            return $this->controllerNameExtractor;
        }

        return new ClassNameExtractor;
    }
}
