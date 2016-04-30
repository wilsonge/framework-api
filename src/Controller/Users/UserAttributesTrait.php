<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Controller\Users;

use Joomla\Application\AbstractApplication;
use Joomla\Input\Input;

trait UserAttributesTrait
{
    /**
     * Instantiate the controller.
     *
     * @param   Input                $input  The input object.
     * @param   AbstractApplication  $app    The application object.
     *
     * @since  1.0
     */
    public function __construct(Input $input = null, AbstractApplication $app = null)
    {
        $this->type = 'users';
        $this->userModel = 'Users';
        parent::__construct($input, $app);
    }

    public function addAttributes(array $item)
    {
        $attributes['username'] = $item['username'];
        $attributes['first_name'] = $item['first_name'];
        $attributes['last_name'] = $item['last_name'];

        return $attributes;
    }
}
