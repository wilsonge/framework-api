<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\View\Xml;

use Joomla\View\AbstractView;

/**
 * Joomla Framework JSON View Class
 *
 * @since  __DEPLOY_VERSION__
 */
class DefaultXmlView extends AbstractView
{
    /**
     * Method to render the view.
     *
     * @return  string  The rendered view.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function render()
    {
        $xml = new \SimpleXMLElement('<root/>');
        $data = $this->getData();
        array_walk_recursive($data, array ($xml, 'addChild'));

        return $xml->asXML();
    }
}
