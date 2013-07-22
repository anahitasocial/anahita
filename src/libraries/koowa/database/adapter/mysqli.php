<?php
/**
 * @version		$Id: mysqli.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Mysqli Database Adapter
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Adapter
 */
class KDatabaseAdapterMysqli extends KDatabaseAdapterAbstract
{
	/**
	 * Quote for named objects
	 *
	 * @var string
	 */
	protected $_name_quote = '`';

	/**
 	 * Map of native MySQL types to generic types used when reading
 	 * table column information.
 	 *
 	 * @var array
 	 */
 	protected $_typemap = array(

 	    // numeric
 	    'smallint'          => 'int',
 	    'int'               => 'int',
 	    'integer'           => 'int',
 	    'bigint'            => 'int',
 		'mediumint'			=> 'int',
 		'smallint'			=> 'int',
 		'tinyint'			=> 'int',
 	    'numeric'			=> 'numeric',
 	    'dec'               => 'numeric',
 	   	'decimal'           => 'numeric',
 	   	'float'				=> 'float'  ,
		'double'            => 'float'  ,
		'real' 				=> 'float'  ,

 		// boolean
 		'bool'				=> 'boolean',
 		'boolean' 			=> 'boolean',

 	   	// date & time
 	   	'date'              => 'date'     ,
 	   	'time'              => 'time'     ,
 	   	'datetime'          => 'timestamp',
 	   	'timestamp'         => 'int'  ,
 	   	'year'				=> 'int'  ,

 	   	// string
 	   	'national char'     => 'string',
 	   	'nchar'             => 'string',
 	   	'char'              => 'string',
 	   	'binary'            => 'string',
 	   	'national varchar'  => 'string',
 	   	'nvarchar'          => 'string',
 	   	'varchar'           => 'string',
 	   	'varbinary'         => 'string',
 		'text'				=> 'string',
 		'mediumtext'		=> 'string',
 		'tinytext'			=> 'string',
 		'longtext'			=> 'string',

 	   	// blob
 	   	'blob'				=> 'raw',
 		'tinyblob'			=> 'raw',
 		'mediumblob'		=> 'raw',
 	   	'longtext'          => 'raw',
 	 	'longblob'          => 'raw',

 		//other
 		'set'				=> 'string',
 		'enum'				=> 'string',
	);

	/**
	 * The database name of the active connection
	 *
	 * @var string
	 */
	protected $_database;

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
    		'options'	=> array(
    			'host'		=> ini_get('mysqli.default_host'),
    			'username'	=> ini_get('mysqli.default_user'),
    			'password'  => ini_get('mysqli.default_pw'),
    			'database'	=> '',
    			'port'		=> ini_get("mysqli.default_port"),
    			'socket'	=> ini_get("mysqli.default_socket")
    		)
        ));

        parent::_initialize($config);
    }

	/**
	 * Connect to the db
	 *
	 * @return KDatabaseAdapterMysqli
	 */
	 public function connect()
	 {
		$oldErrorReporting = error_reporting(0);

		$mysqli = new mysqli(
			$this->_options->host,
			$this->_options->username,
			$this->_options->password,
			$this->_options->database,
			$this->_options->port,
			$this->_options->socket
		);

		error_reporting($oldErrorReporting);

		if (mysqli_connect_errno()) {
			throw new KDatabaseAdapterException('Connect failed: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error(), mysqli_connect_errno());
		}

		// If supported, request real datatypes from MySQL instead of returning everything as a string.
		if (defined('MYSQLI_OPT_INT_AND_FLOAT_NATIVE')) {
			$mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
		}

		$this->_connection = $mysqli;
		$this->_connected  = true;
		$this->_database   = $this->_options->database;

		return $this;
 	}

	/**
	 * Disconnect from db
	 *
	 * @return KDatabaseAdapterMysqli
	 */
	public function disconnect()
	{
		if ($this->isConnected())
		{
			$this->_connection->close();
			$this->_connection = null;
			$this->_connected  = false;
		}

		return $this;
	}

	/**
	 * Check if the connection is active
	 *
	 * @return boolean
	 */
	public function isConnected()
	{
		return ($this->_connection instanceof MySQLi) && @$this->_connection->ping();
	}

	/**
	 * Set the connection
	 *
	 * @param 	resource 	The connection resource
	 * @return  KDatabaseAdapterAbstract
	 * @throws  KDatabaseAdapterException If the resource is not an MySQLi instance
	 */
	public function setConnection($resource)
	{
	    if(!($resource instanceof MySQLi)) {
	        throw new KDatabaseAdapterException('Not a MySQLi connection');
	    }

	    $this->_connection = $resource;
		return $this;
	}

	/**
	 * Get the database name
	 *
	 * @return string	The database name
	 */
	public function getDatabase()
	{
	    if(!isset($this->_database)) {
	        $this->_database = $this->select("SELECT DATABASE()", KDatabase::FETCH_FIELD);
	    }

	    return $this->_database;
	}

	/**
	 * Set the database name
	 *
	 * @param 	string 	The database name
	 * @return  KDatabaseAdapterAbstract
	 */
	public function setDatabase($database)
	{
	    if(!$this->_connection->select_db($database)) {
			throw new KDatabaseException('Could not connect with database : '.$database);
	    }

	    $this->_database = $database;
	    return $this;
	}

	/**
	 * Retrieves the table schema information about the given table
	 *
	 * @param 	string 	A table name or a list of table names
	 * @return	KDatabaseSchemaTable
	 */
	public function getTableSchema($table)
	{
		if(!isset($this->_table_schema[$table]))
		{
			$this->_table_schema[$table] = $this->_fetchTableInfo($table);

			$this->_table_schema[$table]->indexes = $this->_fetchTableIndexes($table);
			$this->_table_schema[$table]->columns = $this->_fetchTableColumns($table);
		}

		return $this->_table_schema[$table];
	}

    /**
     * Lock a table.
     *
     * @param  string  Base name of the table.
     * @param  string  Real name of the table.
     * @return boolean True on success, false otherwise.
     */
    public function lockTable($base, $name)
    {
        $query = 'LOCK TABLES '.$this->quoteName($this->getTableNeedle().$base).' WRITE';

        if($base != $name) {
            $query .= ', '.$this->quoteName($this->getTableNeedle().$name).' READ';
        }

        // Create commandchain context.
        $context = $this->getCommandContext();
        $context->table = $base;
        $context->query = $query;

        if($this->getCommandChain()->run('before.locktable', $context) !== false)
        {
            $context->result = $this->execute($context->query, KDatabase::RESULT_USE);
            $this->getCommandChain()->run('after.locktable', $context);
        }

        return $context->result;
    }

    /**
     * Unlock a table.
     *
     * @return boolean True on success, false otherwise.
     */
    public function unlockTable()
    {
        $query = 'UNLOCK TABLES';

        // Create commandchain context.
        $context = $this->getCommandContext();
        $context->table = $base;
        $context->query = $query;

        if($this->getCommandChain()->run('before.unlocktable', $context) !== false)
        {
            $context->result = $this->execute($context->query, KDatabase::RESULT_USE);
            $this->getCommandChain()->run('after.unlocktable', $context);
        }

        return $context->result;
    }

	/**
	 * Fetch the first field of the first row
	 *
	 * @param	mysqli_result  	The result object. A result set identifier returned by the select() function
	 * @param   integer         The index to use
	 * @return The value returned in the query or null if the query failed.
	 */
	protected function _fetchField($result, $key = 0)
	{
		$return = null;
		if($row = $result->fetch_row( )) {
			$return = $row[(int)$key];
		}

		$result->free();

		return $return;
	}

	/**
	 * Fetch an array of single field results
	 *
	 *
	 * @param	mysqli_result  	The result object. A result set identifier returned by the select() function
	 * @param   integer         The index to use
	 * @return 	array 			A sequential array of returned rows.
	 */
	protected function _fetchFieldList($result, $key = 0)
	{
		$array = array();

		while ($row = $result->fetch_row( )) {
			$array[] = $row[(int)$key];
		}

		$result->free();

		return $array;
	}

	/**
     * Fetch the first row of a result set as an associative array
     *
     * @param 	mysqli_result 	The result object. A result set identifier returned by the select() function
     * @return array
     */
	protected function _fetchArray($result)
	{
		$array = $result->fetch_assoc( );
		$result->free();

		return $array;
	}

	/**
	 * Fetch all result rows of a result set as an array of associative arrays
	 *
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * of the database key.  Returns <var>null</var> if the query fails.
	 *
	 * @param 	mysqli_result  	The result object. A result set identifier returned by the select() function
	 * @param 	string 			The column name of the index to use
	 * @return 	array 	If key is empty as sequential list of returned records.
	 */
	protected function _fetchArrayList($result, $key = '')
	{
		$array = array();
		while ($row = $result->fetch_assoc( ))
		{
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}

		$result->free();

		return $array;
	}

	/**
	 * Fetch the first row of a result set as an object
	 *
	 * @param	mysqli_result  The result object. A result set identifier returned by the select() function
	 * @param object
	 */
	protected function _fetchObject($result)
	{
		$object = $result->fetch_object( );
		$result->free();

		return $object;
	}

	/**
	 * Fetch all rows of a result set as an array of objects
	 *
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * of the database key.  Returns <var>null</var> if the query fails.
	 *
	 * @param	mysqli_result  The result object. A result set identifier returned by the select() function
	 * @param 	string 		   The column name of the index to use
	 * @return 	array 	If <var>key</var> is empty as sequential array of returned rows.
	 */
	protected function _fetchObjectList($result, $key='')
	{
		$array = array();
		while ($row = $result->fetch_object( ))
		{
			if ($key) {
				$array[$row->$key] = $row;
			} else {
				$array[] = $row;
			}
		}

		$result->free();

		return $array;
	}

	/**
     * Safely quotes a value for an SQL statement.
     *
     * @param 	mixed 	The value to quote
     * @return string An SQL-safe quoted value
     */
    protected function _quoteValue($value)
    {
        $value =  '\''.mysqli_real_escape_string( $this->_connection, $value ).'\'';
        return $value;
    }

	/**
	 * Retrieves the table schema information about the given tables
	 *
	 * @param 	array|string 	A table name or a list of table names
	 * @return	DatabaseSchemaTable or NULL if the table doesn't exist
	 */
	protected function _fetchTableInfo($table)
	{
		$result = null;
	    $sql    = $this->quoteValue($this->getTableNeedle().$table);

		if($info  = $this->show( 'SHOW TABLE STATUS LIKE '.$sql, KDatabase::FETCH_OBJECT ))
		{
			//Parse the table raw schema data
            $result = $this->_parseTableInfo($info);
		}

		return $result;
	}

	/**
	 * Retrieves the column schema information about the given table
	 *
	 * @param 	string 	A table name
	 * @return	array	An array of columns
	 */
	protected function _fetchTableColumns($table)
	{
	    $result = array();
	    $sql    = $this->quoteName($this->getTableNeedle().$table);

	    if($columns = $this->show( 'SHOW FULL COLUMNS FROM '.$sql, KDatabase::FETCH_OBJECT_LIST))
		{
		    foreach($columns as $column)
			{
				//Set the table name in the raw info (MySQL doesn't add this)
				$column->Table = $table;

				//Parse the column raw schema data
        		$column = $this->_parseColumnInfo($column, $table);

        		$result[$column->name] = $column;
			}
		}

		return $result;
	}

	/**
	 * Retrieves the index information about the given table
	 *
	 * @param 	string 	A table name
	 * @return	array 	An associative array of indexes by index name
	 */
	protected function _fetchTableIndexes($table)
	{
	    $result = array();
	    $sql    = $this->quoteName($this->getTableNeedle().$table);

	    if($indexes = $this->show('SHOW INDEX FROM '.$sql , KDatabase::FETCH_OBJECT_LIST))
		{
			foreach ($indexes as $index) {
				$result[$index->Key_name][$index->Seq_in_index] = $index;
			}
		}

		return $result;
	}

	/**
	 * Parse the raw table schema information
	 *
	 * @param  	object 	The raw table schema information
	 * @return KDatabaseSchemaTable
	 */
	protected function _parseTableInfo($info)
	{
		$table = new KDatabaseSchemaTable;
 	   	$table->name        = $info->Name;
 	   	$table->engine      = $info->Engine;
 	   	$table->type        = $info->Comment == 'VIEW' ? 'VIEW' : 'BASE';
 	    $table->length      = $info->Data_length;
 	    $table->autoinc     = $info->Auto_increment;
 	    $table->collation   = $info->Collation;
 	    $table->behaviors   = array();
 	    $table->description = $info->Comment != 'VIEW' ? $info->Comment : '';

 	    return $table;
	}

	/**
	 * Parse the raw column schema information
	 *
	 * @param  	object 	The raw column schema information
	 * @return KDatabaseSchemaColumn
	 */
	protected function _parseColumnInfo($info)
	{
		//Parse the filter information from the comment
		$filter = array();
		preg_match('#@Filter\("(.*)"\)#Ui', $info->Comment, $filter);

		list($type, $length, $scope) = $this->_parseColumnType($info->Type);

 	   	$column = $this->getService('koowa:database.schema.column');
 	   	$column->name     = $info->Field;
 	   	$column->type     = $type;
 	   	$column->length   = ($length  ? $length  : null);
 	   	$column->scope    = ($scope ? (int) $scope : null);
 	   	$column->default  = $info->Default;
 	   	$column->required = (bool) ($info->Null != 'YES');
 	    $column->primary  = (bool) ($info->Key == 'PRI');
 	    $column->unique   = (bool) ($info->Key == 'UNI' || $info->Key == 'PRI');
 	    $column->autoinc  = (bool) (strpos($info->Extra, 'auto_increment') !== false);
 	    $column->filter   =  isset($filter[1]) ? explode(',', $filter[1]) : $this->_typemap[$type];

 	 	// Don't keep "size" for integers
 	    if (substr($type, -3) == 'int') {
 	       	$column->length = null;
 	   	}

	    // Get the related fields if the column is primary key or part of a unqiue multi column index
        if($indexes = $this->_table_schema[$info->Table]->indexes)
        {
            foreach($indexes as $index)
            {
                //We only deal with composite-unique indexes
                if(count($index) > 1 && !$index[1]->Non_unique)
                {
                    $fields = array();
	                foreach($index as $field) {
	                    $fields[$field->Column_name] = $field->Column_name;
	                }

                    if(array_key_exists($column->name, $fields))
                    {
	                    unset($fields[$column->name]);
                        $column->related = array_values($fields);

                        $column->unique = true;
		                break;
	                }
                 }
             }
        }

 	    return $column;
	}

	/**
	 * Given a raw column specification, parse into datatype, length, and decimal scope.
	 *
	 * @param string The column specification; for example,
 	 * "VARCHAR(255)" or "NUMERIC(10,2)" or "float(6,2) UNSIGNED" or ENUM('yes','no','maybe')
 	 *
 	 * @return array A sequential array of the column type, size, and scope.
 	 */
	protected function _parseColumnType($spec)
 	{
 	 	$spec    = strtolower($spec);
 	  	$type    = null;
 	   	$length  = null;
 	   	$scope   = null;

 	    // find the type first
	    $type = strtok($spec, '( ');

 	   	// find the parens, if any
	    if (false !== ($pos = strpos($spec, '(')))
	    {
		    // there were parens, so there's at least a length
		    // remove parens to get the size.
		    $length = trim(substr(strtok($spec, ' '), $pos), '()');

		    if($type != 'enum' && $type != 'set')
		    {
			    // A comma in the size indicates a scope.
			    $pos = strpos($length, ',');
			    if ($pos !== false)
			    {
				    $scope  = substr($length, $pos + 1);
				    $length = substr($length, 0, $pos);
			    }

            }
		    else $length = explode(',', str_replace("'", "", $length));
	    }

	    return array($type, $length, $scope);
 	}
}