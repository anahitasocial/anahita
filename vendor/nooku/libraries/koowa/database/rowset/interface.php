<?php
/**
 * @version		$Id: interface.php 4648 2012-05-13 21:47:06Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Rowset
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Database Rowset Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Rowset
 * @uses 		KMixinClass
 */
interface KDatabaseRowsetInterface
{
    /**
     * Returns all data as an array.
     *
     * @param  bool  $modified  If TRUE, only return the modified data. Default FALSE
     * @return array
     */
    public function getData($modified = false);

    /**
     * Set the rowset data based on a named array/hash
     *
     * @param   mixed   $data     Either and associative array, a KDatabaseRow object or object
     * @param   boolean $modified If TRUE, update the modified information for each column being set. Default TRUE
     * @return  \KDatabaseRowsetAbstract
     */
  	 public function setData( $data, $modified = true );

    /**
     * Add rows to the rowset
     *
     * This function will either clone the row object, or create a new instance of the row object for
     * each row being inserted. By default the row will be cloned.
     *
     * @param  array $rows An associative array of row data to be inserted.
     * @param  bool  $new  If TRUE, mark the row(s) as new (i.e. not in the database yet). Default TRUE
     * @return  \KDatabaseRowsetAbstract
     * @see __construct
     */
    public function addData(array $data, $new = true);

    /**
     * Gets the identity column of the rowset
     *
     * @return string
     */
	public function getIdentityColumn();

    /**
     * Returns a KDatabaseRow
     *
     * This functions accepts either a know position or associative array of key/value pairs
     *
     * @param   string|array  $needle The position or the key or an associative array of column data to match
     * @return KDatabaseRow(set)Abstract Returns a row or rowset if successful. Otherwise NULL.
     */
    public function find($needle);

    /**
     * Saves all rows in the rowset to the database
     *
     * @return boolean  If successful return TRUE, otherwise FALSE
     */
    public function save();

    /**
     * Deletes all rows in the rowset from the database
     *
     * @return bool  If successful return TRUE, otherwise FALSE
     */
    public function delete();

    /**
     * Reset the rowset
     *
     * @return bool  If successful return TRUE, otherwise FALSE
     */
    public function reset();

    /**
     * Insert a row into the rowset
     *
     * The row will be stored by it's identity_column if set or otherwise by
     * it's object handle.
     *
     * @param  KDatabaseRowInterface $row
     * @return boolean	TRUE on success FALSE on failure
     * @throws InvalidArgumentException if the object doesn't implement KDatabaseRowInterface
     */
    public function insert(KObjectHandlable $row);

    /**
     * Removes a row from the rowset
     *
     * The row will be removed based on it's identity_column if set or otherwise by
     * it's object handle.
     *
     * @param  KDatabaseRowInterface $row
     * @return \KDatabaseRowsetAbstract
     * @throws InvalidArgumentException if the object doesn't implement KDatabaseRowInterface
     */
    public function extract(KObjectHandlable $row);

    /**
     * Test the connected status of the rowset.
     *
     * @return	bool	Returns TRUE by default.
     */
    public function isConnected();
}