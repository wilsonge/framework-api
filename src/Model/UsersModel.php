<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Model;

use Joomla\Model\AbstractDatabaseModel;

class UsersModel extends AbstractDatabaseModel implements ListModel, ItemModel
{
    public function getTotal()
    {
        $db = $this->getDb();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->quoteName('#__users'));

        $db->setQuery($query);

        return (int) $db->loadResult();
    }

    /**
     * Gets a list of users
     * 
     * @param   integer  $page  The page number to render
     * @param   integer  $size  The number of items to show on each page
     *
     * @return array
     */
    public function getItems($page, $size)
    {
        $db = $this->getDb();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->quoteName('#__users'));
        $db->setQuery($query, ((int) $page * (int) $size), (int) $size);

        return $db->loadAssocList();
    }

    /**
     * Gets a list of users
     *
     * @param   integer  $id  The id of the item
     *
     * @return array
     */
    public function getItem($id)
    {
        $db = $this->getDb();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('id') . ' = ' . (int) $id);
        $db->setQuery($query);

        return $db->loadAssoc();
    }
}
