<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Model;

use Joomla\Model\ModelInterface;

interface ListModel extends ModelInterface
{
    public function getItems($page, $size);

    public function getTotal();
}
