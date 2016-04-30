<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller\Users;

use Wilsonge\Api\Controller\AbstractListController;
use Joomla\Model\ModelInterface;

class Users extends AbstractListController
{
    /**
     * The type of the documents being rendered
     *
     * @var    string
     * @since  1.0
     */
    protected $type = 'users';

    public function addAttributes(array $item, array $attributes)
    {
    }
}