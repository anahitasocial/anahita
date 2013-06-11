<?php
/**
 * @version		$Id: interface.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Database Adapter Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Adapter
 */
interface KDatabaseAdapterInterface
{
	/**
	 * Get a database query object
	 *
	 * @return KDatabaseQuery
	 */
	public function getQuery(KConfig $config = null);

	/**
	 * Connect to the db
	 *
	 * @return  KDatabaseAdapterAbstract
	 */
	public function connect();

	/**
	 * Reconnect to the db
	 *
	 * @return  KDatabaseAdapterAbstract
	 */
	public function reconnect();

	/**
	 * Disconnect from db
	 *
	 * @return  KDatabaseAdapterAbstract
	 */
	public function disconnect();

	/**
	 * Get the connection
	 *
	 * Provides access to the underlying database connection. Useful for when
	 * you need to call a proprietary method such as postgresql's lo_* methods
	 *
	 * @return resource
	 */
	public function getConnection();

	/**
	 * Set the connection
	 *
	 * @param 	resource 	The connection resource
	 * @return  KDatabaseAdapterAbstract
	 */
	public function setConnection($resource);

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return      boolean
	 */
	public function isConnected();

	/**
	 * Get the insert id of the last insert operation
	 *
	 * @return mixed The id of the last inserted row(s)
	 */
 	public function getInsertId();

	/**
	 * Retrieves the column schema information about the given table
	 *
	 * @param 	string 	A table name
	 * @return	KDatabaseSchemaTable
	 */
	public function getTableSchema($table);

    /**
     * Lock a table.
     *
     * @param  string  Base name of the table.
     * @param  string  Real name of the table.
     * @return boolean True on success, false otherwise.
     */
    public function lockTable($base, $name);

    /**
     * Unlock a table.
     *
     * @return boolean True on success, false otherwise.
     */
    public function unlockTable();

	/**
     * Preforms a select query
     *
     * Use for SELECT and anything that returns rows.
     *
     * @param	string  	A full SQL query to run. Data inside the query should be properly escaped.
     * @param	integer 	The result maode, either the constant KDatabase::RESULT_USE or KDatabase::RESULT_STORE
     * 						depending on the desired behavior. By default, KDatabase::RESULT_STORE is used. If you
     * 						use KDatabase::RESULT_USE all subsequent calls will return error Commands out of sync
     * 						unless you free the result first.
     * @return  mixed 		If successfull returns a result object otherwise FALSE
     */
	public function select($sql, $mode = KDatabase::RESULT_STORE);

	/**
     * Preforms a show query
     *
     * @param	string|object  	A full SQL query to run. Data inside the query should be properly escaped.
     * @param   integer			The fetch mode. Controls how the result will be returned to the caller. This
     * 							value must be one of the KDatabase::FETCH_* constants.
     * @return  mixed 			The return value of this function on success depends on the fetch type.
     * 					    	In all cases, FALSE is returned on failure.
     */
	public function show($query, $mode = KDatabase::FETCH_ARRAY_LIST);

	/**
     * Inserts a row of data into a table.
     *
     * Automatically quotes the data values
     *
     * @param string  	The table to insert data into.
     * @param array 	An associative array where the key is the colum name and
     * 					the value is the value to insert for that column.
     * @return integer  If successfull the new rows primary key value, false is no row was inserted.
     */
	public function insert($table, array $data);

	/**
     * Updates a table with specified data based on a WHERE clause
     *
     * Automatically quotes the data values
     *
     * @param string 	The table to update
     * @param array  	An associative array where the key is the column name and
     * 				 	the value is the value to use ofr that column.
     * @param mixed 	A sql string or KDatabaseQuery object to limit which rows are updated.
     * @return integer  If successfull the Number of rows affected, otherwise false
     */
	public function update($table, array $data, $where = null);

	/**
     * Deletes rows from the table based on a WHERE clause.
     *
     * @param string The table to update
     * @param mixed  A query string or a KDatabaseQuery object to limit which rows are updated.
     * @return integer Number of rows affected
     */
	public function delete($table, $where);

	/**
	 * Use and other queries that don't return rows
	 *
	 * @param  string 	The query to run. Data inside the query should be properly escaped.
	 * @param  integer 	The result maode, either the constant KDatabase::RESULT_USE or KDatabase::RESULT_STORE
     * 					depending on the desired behavior. By default, KDatabase::RESULT_STORE is used. If you
     * 					use KDatabase::RESULT_USE all subsequent calls will return error Commands out of sync
     * 					unless you free the result first.
	 * @throws KDatabaseException
	 * @return boolean 	For SELECT, SHOW, DESCRIBE or EXPLAIN will return a result object.
	 * 					For other successful queries  return TRUE.
	 */
	public function execute($sql, $mode = KDatabase::RESULT_STORE );

	/**
	 * Set the table prefix
	 *
	 * @param string The table prefix
	 * @return KDatabaseAdapterAbstract
	 * @see KDatabaseAdapterAbstract::replaceTableNeedle
	 */
	public function setTablePrefix($prefix);

 	/**
	 * Get the table prefix
	 *
	 * @return string The table prefix
	 * @see KDatabaseAdapterAbstract::replaceTableNeedle
	 */
	public function getTablePrefix();

	/**
	 * Get the table needle
	 *
	 * @return string The table needle
	 * @see KDatabaseAdapterAbstract::replaceTableNeedle
	 */
	public function getTableNeedle();

	/**
	 * This function replaces the table needles in a query string with the actual table prefix.
	 *
	 * @param  string 	The SQL query string
	 * @return string	The SQL query string
	 */
	public function replaceTableNeedle( $sql );

    /**
     * Safely quotes a value for an SQL statement.
     *
     * If an array is passed as the value, the array values are quoted
     * and then returned as a comma-separated string; this is useful
     * for generating IN() lists.
     *
     * @param   mixed The value to quote.
     * @return string An SQL-safe quoted value (or a string of separated-
     *                and-quoted values).
     */
    public function quoteValue($value);

    /**
     * Quotes a single identifier name (table, table alias, table column,
     * index, sequence).  Ignores empty values.
     *
     * This function requires all SQL statements, operators and functions to be
     * uppercased.
     *
     * @param string|array The identifier name to quote.  If an array, quotes
     *                      each element in the array as an identifier name.
     * @return string|array The quoted identifier name (or array of names).
     */
    public function quoteName($spec);
}