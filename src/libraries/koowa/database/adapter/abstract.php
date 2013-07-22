<?php
/**
 * @version		$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract Database Adapter
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Adapter
 * @uses 		KPatternCommandChain
 */
abstract class KDatabaseAdapterAbstract extends KObject implements KDatabaseAdapterInterface
{
	/**
	 * Active state of the connection
	 *
	 * @var boolean
	 */
	protected $_connected = null;

	/**
	 * The database connection resource
	 *
	 * @var mixed
	 */
	protected $_connection = null;

	/**
	 * Last auto-generated insert_id
	 *
	 * @var integer
	 */
	protected $_insert_id;

	/**
	 * The affected row count
	 *
	 * @var int
	 */
	protected $_affected_rows;

	/**
	 * Schema cache
	 *
	 * @var array
	 */
	protected $_table_schema = null;

	/**
	 * The table prefix
	 *
	 * @var string
	 */
	protected $_table_prefix = '';

	/**
	 * The table needle
	 *
	 * @var string
	 */
	protected $_table_needle = '';

	/**
	 * Quote for named objects
	 *
	 * @var string
	 */
	protected $_name_quote = '`';

	/**
	 * The connection options
	 *
	 * @var KConfig
	 */
	protected $_options = null;

	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * Recognized key values include 'command_chain', 'charset', 'table_prefix',
	 * (this list is not meant to be comprehensive).
	 */
	public function __construct( KConfig $config = null )
	{
		//If no config is passed create it
		if(!isset($config)) $config = new KConfig();

		// Initialize the options
        parent::__construct($config);

        // Set the connection
        $this->setConnection($config->connection);

		// Set the default charset. http://dev.mysql.com/doc/refman/5.1/en/charset-connection.html
		if (!empty($config->charset)) {
			//$this->setCharset($config->charset);
		}

		// Set the table prefix
		$this->_table_prefix = $config->table_prefix;

		// Set the table prefix
		$this->_table_needle = $config->table_needle;

		// Set the connection options
		$this->_options = $config->options;

		 //Set the mixer in the config
        $config->mixer = $this;
		
		// Mixin the command interface
        $this->mixin(new KMixinCommand($config));
	}

	/**
	 * Destructor
	 *
	 * Free any resources that are open.
	 */
	public function __destruct()
	{
		$this->disconnect();
	}

  	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'options'			=> array(),
    		'charset'			=> 'UTF-8',
       	 	'table_prefix'  	=> 'jos_',
    	    'table_needle'		=> '#__',
    		'command_chain'     => $this->getService('koowa:command.chain'),
    		'dispatch_events'   => true,
    		'event_dispatcher'  => $this->getService('koowa:event.dispatcher'),
    		'enable_callbacks' 	=> false,
    		'connection'		=> null,
        ));

        parent::_initialize($config);
    }

	/**
	 * Get a database query object
	 *
	 * @return KDatabaseQuery
	 */
	public function getQuery(KConfig $config = null)
	{
		if(!isset($config)) {
			$config = new KConfig(array('adapter' => $this));
		}

		return new KDatabaseQuery($config);
	}

	/**
	 * Reconnect to the db
	 *
	 * @return  KDatabaseAdapterAbstract
	 */
	public function reconnect()
	{
		$this->disconnect();
		$this->connect();

		return $this;
	}

	/**
	 * Disconnect from db
	 *
	 * @return  KDatabaseAdapterAbstract
	 */
	public function disconnect()
	{
		$this->_connection = null;
		$this->_connected  = false;

		return $this;
	}

	/**
	 * Get the database name
	 *
	 * @return string	The database name
	 */
	abstract function getDatabase();

	/**
	 * Set the database name
	 *
	 * @param 	string 	The database name
	 * @return  KDatabaseAdapterAbstract
	 */
	abstract function setDatabase($database);

	/**
	 * Get the connection
	 *
	 * Provides access to the underlying database connection. Useful for when
	 * you need to call a proprietary method such as postgresql's lo_* methods
	 *
	 * @return resource
	 */
	public function getConnection()
	{
		return $this->_connection;
	}

	/**
	 * Set the connection
	 *
	 * @param 	resource 	The connection resource
	 * @return  KDatabaseAdapterAbstract
	 */
	public function setConnection($resource)
	{
	    $this->_connection = $resource;
		return $this;
	}

	/**
	 * Get the insert id of the last insert operation
	 *
	 * @return mixed The id of the last inserted row(s)
	 */
 	public function getInsertId()
    {
    	return $this->_insert_id;
    }

	/**
     * Preforms a select query
     *
     * Use for SELECT and anything that returns rows.
     *
     * If <var>key</var> is not empty then the returned array is indexed by the value
	 * of the database key.  Returns <var>null</var> if the query fails.
     *
     * @param	string|object  	A full SQL query to run. Data inside the query should be properly escaped.
     * @param   integer			The fetch mode. Controls how the result will be returned to the caller. This
     * 							value must be one of the KDatabase::FETCH_* constants.
     * @param 	string 			The column name of the index to use
     * @return  mixed 			The return value of this function on success depends on the fetch type.
     * 					    	In all cases, FALSE is returned on failure.
     */
	public function select($query, $mode = KDatabase::FETCH_ARRAY_LIST, $key = '')
	{
		$context = $this->getCommandContext();
		$context->query	 	= $query;
		$context->operation = KDatabase::OPERATION_SELECT;
		$context->mode		= $mode;

		// Excute the insert operation
		if($this->getCommandChain()->run('before.select', $context) !== false)
		{
			if($result = $this->execute( $context->query, KDatabase::RESULT_USE))
			{
				switch($context->mode)
				{
					case KDatabase::FETCH_ARRAY       :
						$context->result = $this->_fetchArray($result);
						break;

					case KDatabase::FETCH_ARRAY_LIST  :
						$context->result = $this->_fetchArrayList($result, $key);
						break;

					case KDatabase::FETCH_FIELD       :
						$context->result = $this->_fetchField($result, $key);
						break;

					case KDatabase::FETCH_FIELD_LIST  :
						$context->result = $this->_fetchFieldList($result, $key);
						break;

					case KDatabase::FETCH_OBJECT      :
						$context->result = $this->_fetchObject($result);
						break;

					case KDatabase::FETCH_OBJECT_LIST :
						$context->result = $this->_fetchObjectList($result, $key);
						break;

					default : $result->free();
				}
			}

			$this->getCommandChain()->run('after.select', $context);
		}

		return KConfig::unbox($context->result);
	}

	/**
     * Preforms a show query
     *
     * @param	string|object  	A full SQL query to run. Data inside the query should be properly escaped.
     * @param   integer			The fetch mode. Controls how the result will be returned to the caller. This
     * 							value must be one of the KDatabase::FETCH_* constants.
     * @return  mixed 			The return value of this function on success depends on the fetch type.
     * 					    	In all cases, FALSE is returned on failure.
     */
	public function show($query, $mode = KDatabase::FETCH_ARRAY_LIST)
	{
		$context = $this->getCommandContext();
		$context->query	 	= $query;
		$context->operation = KDatabase::OPERATION_SHOW;
		$context->mode		= $mode;

		// Excute the insert operation
		if($this->getCommandChain()->run('before.show', $context) !== false)
		{
			if($result = $this->execute( $context->query, KDatabase::RESULT_USE))
			{
				switch($context->mode)
				{
					case KDatabase::FETCH_ARRAY       :
						$context->result = $this->_fetchArray($result);
						break;

					case KDatabase::FETCH_ARRAY_LIST  :
						$context->result = $this->_fetchArrayList($result);
						break;

					case KDatabase::FETCH_FIELD       :
						$context->result = $this->_fetchField($result);
						break;

					case KDatabase::FETCH_FIELD_LIST  :
						$context->result = $this->_fetchFieldList($result);
						break;

					case KDatabase::FETCH_OBJECT      :
						$context->result = $this->_fetchObject($result);
						break;

					case KDatabase::FETCH_OBJECT_LIST :
						$context->result = $this->_fetchObjectList($result);
						break;

					default : $result->free();
				}
			}

			$this->getCommandChain()->run('after.show', $context);
		}

		return KConfig::unbox($context->result);
	}

	/**
     * Inserts a row of data into a table.
     *
     * Automatically quotes the data values
     *
     * @param string  	The table to insert data into.
     * @param array 	An associative array where the key is the colum name and
     * 					the value is the value to insert for that column.
     * @return bool|integer  If the insert query was executed returns the number of rows updated, or 0 if
     * 					     no rows where updated, or -1 if an error occurred. Otherwise FALSE.
     */
	public function insert($table, array $data)
	{
		$context = $this->getCommandContext();
		$context->table 	= $table;
		$context->data 		= $data;
		$context->operation	= KDatabase::OPERATION_INSERT;

		//Excute the insert operation
		if($this->getCommandChain()->run('before.insert', $context) !== false)
		{
			//Check if we have valid data to insert, if not return false
			if(count($context->data))
			{
				foreach($context->data as $key => $val)
				{
					$vals[] = $this->quoteValue($val);
					$keys[] = '`'.$key.'`';
				}

				$context->query = 'INSERT INTO '.$this->quoteName($this->getTableNeedle().$context->table )
					 . '('.implode(', ', $keys).') VALUES ('.implode(', ', $vals).')';

				//Execute the query
				$context->result = $this->execute($context->query);

				$context->affected = $this->_affected_rows;

				$this->getCommandChain()->run('after.insert', $context);
			}
			else $context->affected = false;
		}

		return $context->affected;
	}

	/**
     * Updates a table with specified data based on a WHERE clause
     *
     * Automatically quotes the data values
     *
     * @param string 	The table to update
     * @param array  	An associative array where the key is the column name and
     * 				 	the value is the value to use ofr that column.
     * @param mixed 	A sql string or KDatabaseQuery object to limit which rows are updated.
     * @return integer  If the update query was executed returns the number of rows updated, or 0 if
     * 					no rows where updated, or -1 if an error occurred. Otherwise FALSE.
     */
	public function update($table, array $data, $where = null)
	{
		$context = $this->getCommandContext();
		$context->table 	= $table;
		$context->data  	= $data;
		$context->where   	= $where;
		$context->operation	= KDatabase::OPERATION_UPDATE;

		//Excute the update operation
		if($this->getCommandChain()->run('before.update', $context) !==  false)
		{
			if(count($context->data))
			{
				foreach($context->data as $key => $val) {
					$vals[] = '`'.$key.'` = '.$this->quoteValue($val);
				}

				//Create query statement
				$context->query = 'UPDATE '.$this->quoteName($this->getTableNeedle().$context->table)
			  		.' SET '.implode(', ', $vals)
			  		.' '.$context->where
				;

				//Execute the query
				$context->result = $this->execute($context->query);

				$context->affected = $this->_affected_rows;
				$this->getCommandChain()->run('after.update', $context);
			}
			else $context->affected = false;
		}

        return $context->affected;
	}

	/**
     * Deletes rows from the table based on a WHERE clause.
     *
     * @param string 	The table to update
     * @param mixed  	A query string or a KDatabaseQuery object to limit which rows are updated.
     * @return integer 	Number of rows affected, or -1 if an error occured.
     */
	public function delete($table, $where)
	{
		$context = $this->getCommandContext();
		$context->table 	= $table;
		$context->data  	= null;
		$context->where   	= $where;
		$context->operation	= KDatabase::OPERATION_DELETE;

		//Excute the delete operation
		if($this->getCommandChain()->run('before.delete', $context) !== false)
		{
			//Create query statement
			$context->query = 'DELETE FROM '.$this->quoteName($this->getTableNeedle().$context->table)
				  .' '.$context->where
			;

			//Execute the query
			$context->result = $this->execute($context->query);

			$context->affected = $this->_affected_rows;
			$this->getCommandChain()->run('after.delete', $context);
		}

		return $context->affected;
	}

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
	public function execute($sql, $mode = KDatabase::RESULT_STORE )
	{
		//Replace the database table prefix
		$sql = $this->replaceTableNeedle( $sql );

		$result = $this->_connection->query($sql, $mode);

		if($result === false) {
			throw new KDatabaseException($this->_connection->error.' of the following query : '.$sql, $this->_connection->errno);
		}

		$this->_affected_rows = $this->_connection->affected_rows;
		$this->_insert_id     = $this->_connection->insert_id;

		return $result;
	}

	/**
	 * Set the table prefix
	 *
	 * @param string The table prefix
	 * @return KDatabaseAdapterAbstract
	 * @see KDatabaseAdapterAbstract::replaceTableNeedle
	 */
	public function setTablePrefix($prefix)
	{
		$this->_table_prefix = $prefix;
		return $this;
	}

 	/**
	 * Get the table prefix
	 *
	 * @return string The table prefix
	 * @see KDatabaseAdapterAbstract::replaceTableNeedle
	 */
	public function getTablePrefix()
	{
		return $this->_table_prefix;
	}

	/**
	 * Get the table needle
	 *
	 * @return string The table needle
	 * @see KDatabaseAdapterAbstract::replaceTableNeedle
	 */
	public function getTableNeedle()
	{
		return $this->_table_needle;
	}

	/**
	 * This function replaces the table needles in a query string with the actual table prefix.
	 *
	 * @param  string 	The SQL query string
	 * @return string	The SQL query string
	 */
	public function replaceTableNeedle( $sql, $replace = null)
	{
		$needle  = $this->getTableNeedle();
	    $replace = isset($replace) ? $replace : $this->getTablePrefix();
		$sql     = trim( $sql );

		$pattern = "($needle(?=[a-z0-9]))";
    	$sql = preg_replace($pattern, $replace, $sql);

		return $sql;
	}

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
    public function quoteValue($value)
    {
        if (is_array($value))
        {
            //Quote array values, not keys, then combine with commas.
            foreach ($value as $k => $v) {
                $value[$k] = $this->quoteValue($v);
            }

            $value = implode(', ', $value);
        }
        else
        {
            if(is_string($value) && !is_null($value)) {
                $value = $this->_quoteValue($value);
            }
        }

        return $value;
    }

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
     *
     * @see _quoteName()
     */
    public function quoteName($spec)
    {
        if (is_array($spec))
        {
            foreach ($spec as $key => $val) {
                $spec[$key] = $this->quoteName($val);
            }

            return $spec;
        }

        // String spaces around the identifier
        $spec = trim($spec);

        // Quote all the lower case parts
        $spec = preg_replace_callback('#(?:\b|\#)+(?<!`)([a-z0-9\.\#\-_]+)(?!`)\b#', array($this, '_quoteName') , $spec);

        return $spec;
    }

   /**
     * Fetch the first field of the first row
     *
     * @param   mysqli_result   The result object. A result set identifier returned by the select() function
     * @param   integer         The index to use
     * @return The value returned in the query or null if the query failed.
     */
    abstract protected function _fetchField($result, $key = 0);

    /**
     * Fetch an array of single field results
     *
     * @param   mysqli_result   The result object. A result set identifier returned by the select() function
     * @param   integer         The index to use
     * @return  array           A sequential array of returned rows.
     */
    abstract protected function _fetchFieldList($result, $key = 0);

    /**
     * Fetch the first row of a result set as an associative array
     *
     * @param   mysqli_result   The result object. A result set identifier returned by the select() function
     * @return array
     */
    abstract protected function _fetchArray($sql);

    /**
     * Fetch all result rows of a result set as an array of associative arrays
     *
     * If <var>key</var> is not empty then the returned array is indexed by the value
     * of the database key.  Returns <var>null</var> if the query fails.
     *
     * @param   mysqli_result   The result object. A result set identifier returned by the select() function
     * @param   string          The column name of the index to use
     * @return  array   If key is empty as sequential list of returned records.
     */
    abstract protected function _fetchArrayList($result, $key = '');

    /**
     * Fetch the first row of a result set as an object
     *
     * @param   mysqli_result  The result object. A result set identifier returned by the select() function
     * @param object
     */
    abstract protected function _fetchObject($result);

    /**
     * Fetch all rows of a result set as an array of objects
     *
     * If <var>key</var> is not empty then the returned array is indexed by the value
     * of the database key.  Returns <var>null</var> if the query fails.
     *
     * @param   mysqli_result  The result object. A result set identifier returned by the select() function
     * @param   string         The column name of the index to use
     * @return  array   If <var>key</var> is empty as sequential array of returned rows.
     */
    abstract protected function _fetchObjectList($result, $key='' );

    /**
     * Parse the raw table schema information
     *
     * @param   object  The raw table schema information
     * @return KDatabaseSchemaTable
     */
    abstract protected function _parseTableInfo($info);

    /**
     * Parse the raw column schema information
     *
     * @param   object  The raw column schema information
     * @return KDatabaseSchemaColumn
     */
    abstract protected function _parseColumnInfo($info);

    /**
     * Given a raw column specification, parse into datatype, size, and decimal scope.
     *
     * @param string The column specification; for example,
     * "VARCHAR(255)" or "NUMERIC(10,2)".
     *
     * @return array A sequential array of the column type, size, and scope.
     */
    abstract protected function _parseColumnType($spec);

    /**
     * Safely quotes a value for an SQL statement.
     *
     * @param   mixed   The value to quote
     * @return string An SQL-safe quoted value
     */
    abstract protected function _quoteValue($value);

    /**
     * Quotes an identifier name (table, index, etc). Ignores empty values.
     *
     * If the name contains a dot, this method will separately quote the
     * parts before and after the dot.
     *
     * @param string    The identifier name to quote.
     * @return string   The quoted identifier name.
     * @see quoteName()
     */
    protected function _quoteName($name)
    {
        $result =  '';

        if(is_array($name)) {
            $name = $name[0];
        }

        $name   = trim($name);

        //Special cases
        if ($name == '*' || is_numeric($name)) {
            return $name;
        }

        if ($pos = strrpos($name, '.'))
        {
            $table  = $this->_quoteName(substr($name, 0, $pos));
            $column = $this->_quoteName(substr($name, $pos + 1));

            $result =  "$table.$column";
        }
        else $result = $this->_name_quote. $name.$this->_name_quote;

        return $result;
    }
}