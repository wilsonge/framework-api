<?php
/**
 * @copyright  Copyright (C) 2016 George Wilson. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Wilsonge\Api\Model;

use Joomla\Model\AbstractDatabaseModel;

class UsersModel extends AbstractDatabaseModel implements ListModel, ItemModel
{
    private $primaryKey = 'id';

    /**
     * Gets the total number of users
     *
     * @return int
     */
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
     * Gets a single user
     *
     * @param   integer  $id  The id of the user
     *
     * @return  array
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

    /**
     * Updates an item
     *
     * @param   integer  $id       The id of the item
     * @param   array    $newData  The data to update the item with
     *
     * @return  bool
     */
    public function updateItem($id, array $newData)
    {
        $db = $this->getDb();
        $newData[$this->primaryKey] = $id;
        $dataToSave = (object) $newData;
        return (bool) $db->updateObject('#__users', $dataToSave, array($this->primaryKey));
    }

    /**
     * Creates an item
     *
     * @param   array  $data  The data to create the item with
     *
     * @return  int  The primary key of the newly created item
     */
    public function createItem(array $data)
    {
        $db = $this->getDb();
        $dataToSave = (object) $data;
        $db->insertObject('#__users', $dataToSave, array($this->primaryKey));
        return $dataToSave->{$this->primaryKey};
    }
}
