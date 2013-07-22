<?php
/**
 * @version		$Id: interface.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Row
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Database Row Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Row
 */
interface KDatabaseRowInterface
{
	/**
     * Returns the status of this row.
     *
     * @return string The status value.
     */
    public function getStatus();

	/**
     * Load the row from the database.
     *
     * @return object	If successfull returns the row object, otherwise NULL
     */
	public function load();

    /**
     * Saves the row to the database.
     *
     * This performs an intelligent insert/update and reloads the properties
     * with fresh data from the table on success.
     *
     * @return KDatabaseRowInterface
     */
    public function save();

    /**
     * Deletes the row form the database.
     *
     * @return KDatabaseRowInterface
     */
    public function delete();

    /**
     * Count the rows in the database based on the data in the row
     *
     * @return KDatabaseRowAbstract
     */
    public function count();

    /**
     * Resets to the default properties
     *
     * @return KDatabaseRowInterface
     */
    public function reset();

   /**
    * Returns an associative array of the raw data
    *
    * @param   boolean  If TRUE, only return the modified data. Default FALSE
    * @return  array
    */
    public function getData($modified = false);

    /**
     * Set the row data
     *
     * @param   mixed   Either and associative array, an object or a KDatabaseRow
     * @param   boolean If TRUE, update the modified information for each column being set.
     *                  Default TRUE
     * @return  KDatabaseRowInterface
     */
     public function setData( $data, $modified = true );

    /**
     * Get a list of columns that have been modified
     *
     * @return array    An array of column names that have been modified
     */
    public function getModified();

    /**
     * Check if a column has been modified
     *
     * @param   string  The column name.
     * @return  boolean
     */
    public function isModified($column);

    /**
     * Checks if the row is new or not
     *
     * @return bool
     */
    public function isNew();

	/**
	 * Test the connected status of the row.
	 *
	 * @return	bool
	 */
    public function isConnected();
}