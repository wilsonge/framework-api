<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Model;

use Joomla\Model\ModelInterface;

interface ItemModel extends ModelInterface
{
    /**
     * Gets a single item
     *
     * @param   mixed  $id  The primary key of the item
     *
     * @return  array
     */
    public function getItem($id);

    /**
     * Updates an item
     *
     * @param   integer  $id       The id of the item
     * @param   array    $newData  The data to update the item with
     *
     * @return  bool
     */
    public function updateItem($id, array $newData);

    /**
     * Creates an item
     *
     * @param   array  $data  The data to create the item with
     *
     * @return  int  The primary key of the newly created item
     */
    public function createItem(array $data);
}
