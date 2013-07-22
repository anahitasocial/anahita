<?php
/**
 * @version		$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Table
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract Table Class
 *
 * Parent class to all tables.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Table
 * @uses        KMixinClass
 * @uses        KFilter
 */
abstract class KDatabaseTableAbstract extends KObject
{
    /**
     * Real name of the table in the db schema
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Base name of the table in the db schema
     *
     * @var string
     */
    protected $_base;
    
    /**
     * Name of the identity column in the table
     *
     * @var string
     */
    protected $_identity_column;
    
    /**
     * Array of column mappings by column name
     *
     * @var array
     */
    protected $_column_map = array();
    
    /**
     * Database adapter
     *
     * @var object
     */
    protected $_database = false;
    
    /**
     * Default values for this table
     *
     * @var array
     */
    protected $_defaults;
    
    /**
     * Object constructor 
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();
        
        parent::__construct($config);
        
        $this->_name        = $config->name;
        $this->_base        = $config->base;
        $this->_database    = $config->database;
        
        //Check if the table exists
        if(!$info = $this->getSchema()) {
            throw new KDatabaseTableException('Table '.$this->_name.' does not exist');
        }
            
        // Set the identity column
        if(!isset($config->identity_column)) 
        {
            foreach ($this->getColumns(true) as $column)
            {
                if($column->autoinc) {
                    $this->_identity_column = $column->name;
                    break;
                }
            }
        }
        else $this->_identity_column = $config->identity_column;
        
        //Set the default column mappings
         $this->_column_map = $config->column_map ? $config->column_map->toArray() : array();
         if(!isset( $this->_column_map['id']) && isset($this->_identity_column)) {
            $this->_column_map['id'] = $this->_identity_column;
         }
           
        // Set the column filters
        if(!empty($config->filters)) 
        {
            foreach($config->filters as $column => $filter) {
                $this->getColumn($column, true)->filter = KConfig::unbox($filter);
            }       
        }
        
        //Set the mixer in the config
        $config->mixer = $this;
        
        // Mixin the command interface
        $this->mixin(new KMixinCommand($config));
         
        // Mixin the behavior interface
        $this->mixin(new KMixinBehavior($config));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $package = $this->getIdentifier()->package;
        $name    = $this->getIdentifier()->name;
        
        $config->append(array(
            'database'          => $this->getService('koowa:database.adapter.mysqli'),
            'name'              => empty($package) ? $name : $package.'_'.$name,
            'column_map'        => null,
            'filters'           => array(),
            'behaviors'         => array(),
            'identity_column'   => null,
            'command_chain'     => $this->getService('koowa:command.chain'),
            'dispatch_events'   => false,
            'event_dispatcher'  => null,
            'enable_callbacks'  => false,
        ))->append(
            array('base'        => $config->name)
        );
        
         parent::_initialize($config);
    }
    
    /**
     * Get the database adapter
     *
     * @return KDatabaseAdapterAbstract
     */
    public function getDatabase()
    {
        return $this->_database;
    }

    /**
     * Set the database adapter
     *
     * @param   object A KDatabaseAdapterAbstract
     * @return  KDatabaseTableAbstract
     */
    public function setDatabase(KDatabaseAdapterAbstract $database)
    {
        $this->_database = $database;
        return $this;
    }
    
	/**
	 * Test the connected status of the table
	 *
	 * @return	boolean	Returns TRUE if we have a reference to a live KDatabaseAdapterAbstract object.
	 */
    public function isConnected()
	{
	    return (bool) $this->getDatabase();
	}

    /**
     * Gets the table schema name without the table prefix
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the base table name without the table prefix
     * 
     * If the table type is 'VIEW' the base name will be the name of the base 
     * table that is connected to the view. If the table type is 'BASE' this
     * function will return the same as {@link getName}
     *
     * @return string
     */
    public function getBase()
    {
        return $this->_base;
    }
    
    /**
     * Gets the primary key(s) of the table
     *
     * @return array    An asscociate array of fields defined in the primary key
     */
    public function getPrimaryKey()
    {
        $keys = array();
        $columns = $this->getColumns(true);
            
        foreach ($columns as $name => $description)
        {
            if($description->primary) {
                $keys[$name] = $description;
            }
        }

        return $keys;
    }
    
    /**
     * Gets the schema of the table
     *
     * @return  object|null Returns a KDatabaseSchemaTable object or NULL if the table doesn't exists
     * @throws  KDatabaseTableException
     */
    public function getSchema()
    {
        $result = null;
        
        if($this->isConnected())
        {
            try {
                $result = $this->_database->getTableSchema($this->getBase());
            } catch(KDatabaseException $e) {
                throw new KDatabaseTableException($e->getMessage());
            }
        }
            
        return $result;
    }
      
    /**
     * Get a column by name
     *
     * @param  boolean  If TRUE, get the column information from the base table. Default is FALSE.
     * @return KDatabaseColumn  Returns a KDatabaseSchemaColumn object or NULL if the 
     *                          column does not exist
     */
     public function getColumn($columnname, $base = false)
     {
        $columns = $this->getColumns($base);
        return isset($columns[$columnname]) ? $columns[$columnname] : null;
     }

    /**
     * Gets the columns for the table
     *
     * @param   boolean  If TRUE, get the column information from the base table. Default is FALSE.
     * @return  array    Associative array of KDatabaseSchemaColumn objects
     * @throws  KDatabaseTableException
     */
    public function getColumns($base = false)
    {
        //Get the table name
        $name = $base ? $this->getBase() : $this->getName();
        
        //Get the columns from the schema
        $columns = $this->getSchema($name)->columns;
     
        return $this->mapColumns($columns, true);
    }
    
    /**
     * Table map method
     * 
     * This functions maps the column names to those in the table schema 
     *
     * @param  array|string An associative array of data to be mapped, or a column name
     * @param  boolean      If TRUE, perform a reverse mapping
     * @return array|string The mapped data or column name
     */
    public function mapColumns($data, $reverse = false)
    {
        $map = $reverse ? array_flip($this->_column_map) : $this->_column_map;
        
        $result = null;
        
        if(is_array($data))
        {
            $result = array();
        
            foreach($data as $column => $value)
            {
                if(is_string($column))
                {
                    //Map the key
                    if(isset($map[$column])) {
                        $column = $map[$column];
                    }
                }
                else
                {
                    //Map the value
                    if (isset($map[$value])) {
                        $value = $map[$value];
                    }
                }
                
                $result[$column] = $value;
            }
        }
        
        if(is_string($data))
        {
            $result = $data;
            if(isset($map[$data])) {
                $result = $map[$data];
            }
        }
        
        return $result;
        
       
    }
            
    /**
     * Gets the identitiy column of the table.
     *
     * @return string
     */
    public function getIdentityColumn()
    {
        $result = null;
        if(isset($this->_identity_column)) {
            $result = $this->_identity_column;
        }
        
        return $result;
    }
    
    /**
     * Gets the unqiue columns of the table
     *
     * @return array    An asscociate array of unique table columns by column name
     */
    public function getUniqueColumns()
    {
        $result  = array();
        $columns = $this->getColumns(true);
        
        foreach($columns as $name => $description)
        {
            if($description->unique) {
                $result[$name] = $description;
            }
        }
        
        return $result;
    }
     
    /**
     * Get default values for all columns
     * 
     * @return  array
     */
    public function getDefaults()
    {
        if(!isset($this->_defaults))
        {
            $defaults = array();
            $columns  = $this->getColumns();
            
            foreach($columns as $name => $description) {
                $defaults[$name] = $description->default;
            }
            
            $this->_defaults = $defaults;
        }
         
        return $this->_defaults;
    }
    
    /**
     * Get a default by name
     *
     * @return mixed    Returns the column default value or NULL if the 
     *                  column does not exist
     */
    public function getDefault($columnname)
    {
        $defaults = $this->getDefaults();
        return isset($defaults[$columnname]) ? $defaults[$columnname] : null;
    }
    
    /**
     * Get an instance of a row object for this table
     *
     * @param	array An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function getRow(array $options = array())
    {
        $identifier         = clone $this->getIdentifier();
        $identifier->path   = array('database', 'row');
        $identifier->name   = KInflector::singularize($this->getIdentifier()->name);
            
        //The row default options
        $options['table'] = $this; 
        $options['identity_column'] = $this->mapColumns($this->getIdentityColumn(), true);
             
        return $this->getService($identifier, $options); 
    }
    
    /**
     * Get an instance of a rowset object for this table
     *
     * @param	array An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function getRowset(array $options = array())
    {
        $identifier         = clone $this->getIdentifier();
        $identifier->path   = array('database', 'rowset');
            
        //The rowset default options
        $options['table'] = $this; 
        $options['identity_column'] = $this->mapColumns($this->getIdentityColumn(), true);
        
        return $this->getService($identifier, $options);
    }
    
    /**
     * Table select method
     *
     * The name of the resulting row(set) class is based on the table class name
     * eg Com<Mycomp>Table<Tablename> -> Com<Mycomp>Row(set)<Tablename>
     * 
     * This function will return an empty rowset if called without a parameter.
     *
     * @param   mixed       KDatabaseQuery, query string, array of row id's, or an id or null
     * @param   integer     The database fetch style. Default FETCH_ROWSET.
     * @return  KDatabaseRow or KDatabaseRowset depending on the mode. By default will 
     *          return a KDatabaseRowset 
     */
    public function select( $query = null, $mode = KDatabase::FETCH_ROWSET)
    {
       //Create query object
        if(is_numeric($query) || is_string($query) || (is_array($query) && is_numeric(key($query))))
        {
            $key    = $this->getIdentityColumn();
            $values = (array) $query;

            $query = $this->_database->getQuery()
                        ->where($key, 'IN', $values);
        }
        
        if(is_array($query) && !is_numeric(key($query)))
        {
            $columns = $this->mapColumns($query);
            $query   = $this->_database->getQuery();    
            
            foreach($columns as $column => $value) {
                $query->where($column, 'IN', $value);
            }
        }
        
        if($query instanceof KDatabaseQuery)
        {
            if(!is_null($query->columns) && !count($query->columns)) {
                $query->select('*');
            }

            if(!count($query->from)) {
                $query->from($this->getName().' AS tbl');
            }
        }
            
        //Create commandchain context
        $context = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_SELECT;
        $context->query     = $query;
        $context->table     = $this->getBase();
        $context->mode      = $mode;
        
        if($this->getCommandChain()->run('before.select', $context) !== false) 
        {                   
            //Fetch the data based on the fecthmode
            if($context->query)
            {
                $data = $this->_database->select($context->query, $context->mode, $this->getIdentityColumn());
                
                //Map the columns
                if (($context->mode != KDatabase::FETCH_FIELD) || ($context->mode != KDatabase::FETCH_FIELD_LIST))
                { 
                    if($context->mode % 2)
                    {
                        foreach($data as $key => $value) {
                            $data[$key] = $this->mapColumns($value, true);
                        }
                    }
                    else $data = $this->mapColumns(KConfig::unbox($data), true);   
                }
            }
            
            switch($context->mode)
            {
                case KDatabase::FETCH_ROW    : 
                {
                    $options = array();
                    if(isset($data) && !empty($data)) 
                    {
                        $options = array(
                    		'data'   => $data,
                        	'new'    => false,
                            'status' => KDatabase::STATUS_LOADED
                        );
                    }

                    $context->data = $this->getRow($options);
                    break;
                }
                
                case KDatabase::FETCH_ROWSET : 
                {
                    $options = array();
                    if(isset($data) && !empty($data)) 
                    {
                        $options = array(
                    		'data'   => $data,
                        	'new'    => false,
                        );
                    }
                    
                    $context->data = $this->getRowset($options);
                    break;
                }
                
                default : $context->data = $data;
            }
                        
            $this->getCommandChain()->run('after.select', $context);
        }
    
        return KConfig::unbox($context->data);
    }
    
    /**
     * Count table rows
     *
     * @param   mixed   KDatabaseQuery object or query string or null to count all rows
     * @return  int     Number of rows
     */
    public function count($query = null)
    {
        //Count using the identity column
        if (is_scalar($query)) 
    	{
    		$key   = $this->getIdentityColumn();
    		$query = array($key => $query);
    	}
    	
        //Create query object
        if(is_array($query) && !is_numeric(key($query)))
        {
            $columns = $this->mapColumns($query);
            
            $query   = $this->_database->getQuery();    
            foreach($columns as $column => $value) {
                $query->where($column, '=', $value);
            }               
        }
            
        if($query instanceof KDatabaseQuery)
        {
            $query->count();

            if(!count($query->from)) {
                $query->from($this->getName().' AS tbl');
            }
        }
            
        $result = (int) $this->select($query, KDatabase::FETCH_FIELD);   
        return $result;
    }

    /**
     * Table insert method
     *
     * @param  object   	A KDatabaseRow object
     * @return bool|integer Returns the number of rows inserted, or FALSE if insert query was not executed.
     */
    public function insert( KDatabaseRowInterface $row )
    {
        //Create commandchain context
        $context = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_INSERT;
        $context->data      = $row;
        $context->table     = $this->getBase();
        $context->query     = null;
        $context->affected  = false;
        
        if($this->getCommandChain()->run('before.insert', $context) !== false) 
        {
            //Filter the data and remove unwanted columns
            $data = $this->filter($context->data->getData(), true);
            
            //Get the data and apply the column mappings
            $data = $this->mapColumns($data);
            
            //Execute the insert query
            $context->affected = $this->_database->insert($context->table, $data);
            
            if($context->affected !== false) 
            {
                if(((integer) $context->affected) > 0)
                {
                    if($this->getIdentityColumn()) {
                        $data[$this->getIdentityColumn()] = $this->_database->getInsertId();
                    }
                    
                    //Reverse apply the column mappings and set the data in the row
                    $context->data->setData($this->mapColumns($data, true), false)
                                  ->setStatus(KDatabase::STATUS_CREATED);
                }
                else $context->data->setStatus(KDatabase::STATUS_FAILED);
            }
                
            $this->getCommandChain()->run('after.insert', $context);
        }

        return $context->affected;
    }

    /**
     * Table update method
     *
     * @param  object   		A KDatabaseRow object
     * @return boolean|integer  Returns the number of rows updated, or FALSE if insert query was not executed.
     */
    public function update( KDatabaseRowInterface $row)
    {
        //Create commandchain context
        $context = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_UPDATE;
        $context->data      = $row;
        $context->table     = $this->getBase();
        $context->query     = null;
        $context->affected  = false;

        if($this->getCommandChain()->run('before.update', $context) !== false) 
        {
            //Create where statement
            $query = $this->_database->getQuery();
            
            //@TODO : Gracefully handle error if not all primary keys are set in the row
            foreach($this->getPrimaryKey() as $key => $column) {
                $query->where($column->name, '=', $this->filter(array($key => $context->data->$key), true));
            }
        
            //Filter the data and remove unwanted columns
            $data = $this->filter($context->data->getData(true), true);
            
            //Get the data and apply the column mappings
            $data = $this->mapColumns($data);
                     	  			
            //Execute the update query
            $context->affected = $this->_database->update($context->table, $data, $query);
	
            if($context->affected !== false) 
            {
                if(((integer) $context->affected) > 0)
                {
                    //Reverse apply the column mappings and set the data in the row
                    $context->data->setData($this->mapColumns($data, true), false)
                                  ->setStatus(KDatabase::STATUS_UPDATED);
                }
                else $context->data->setStatus(KDatabase::STATUS_FAILED);
            }      
              
            //Set the query in the context
            $context->query = $query;
            
            $this->getCommandChain()->run('after.update', $context);
        }

        return $context->affected;
    }

    /**
     * Table delete method
     *
     * @param  object   	A KDatabaseRow object
     * @return bool|integer Returns the number of rows deleted, or FALSE if delete query was not executed.
     */
    public function delete( KDatabaseRowInterface $row )
    {
        //Create commandchain context
        $context = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_DELETE;
        $context->table     = $this->getBase();
        $context->data      = $row;
        $context->query     = null;
        $context->affected  = false;
        
        if($this->getCommandChain()->run('before.delete', $context) !== false) 
        {
            $query = $this->_database->getQuery();
            
            //Create where statement
            foreach($this->getPrimaryKey() as $key => $column) {
                $query->where($column->name, '=', $context->data->$key);
            }
            
            //Execute the delete query
            $context->affected = $this->_database->delete($context->table, $query);
            
            //Set the query in the context
            if($context->affected !== false) 
            {
                if(((integer) $context->affected) > 0) 
                {   
                    $context->query = $query;
                    $context->data->setStatus(KDatabase::STATUS_DELETED);
                }
                else $context->data->setStatus(KDatabase::STATUS_FAILED);
            }
            
            $this->getCommandChain()->run('after.delete', $context);
        }

        return $context->affected;
    }
    
 	/**
     * Lock the table.
     * 
     * return boolean True on success, false otherwise.
     */
    public function lock()
    {
        $result = null;
        
        // Create commandchain context.
        $context = $this->getCommandContext();
        $context->table = $this->getBase();
        
        if($this->getCommandChain()->run('before.lock', $context) !== false) 
        {
            if($this->isConnected()) 
            {
                try {
                    $context->result = $this->_database->lockTable($this->getBase(), $this->getName());
                } catch(KDatabaseException $e) {
                    throw new KDatabaseTableException($e->getMessage());
                }
            }
            
            $this->getCommandChain()->run('after.lock', $context);
        }
        
        return $context->result;
    }
    
    /**
     * Unlock the table.
     * 
     * return boolean True on success, false otherwise.
     */
    public function unlock()
    {
        $result = null;
        
        // Create commandchain context.
        $context = $this->getCommandContext();
        $context->table = $this->getBase();
        
        if($this->getCommandChain()->run('before.unlock', $context) !== false) 
        {
            if($this->isConnected()) 
            {
                try {
                    $context->result = $this->_database->unlockTable();
                } catch(KDatabaseException $e) {
                    throw new KDatabaseTableException($e->getMessage());
                }
            }
            
            $this->getCommandChain()->run('after.unlock', $context);
        }
        
        return $context->result;
    }

    /**
     * Table filter method
     *
     * This function removes extra columns based on the table columns taking any table mappings into
     * account and filters the data based on each column type.
     *
     * @param   boolean  If TRUE, get the column information from the base table. Default is TRUE.
     * @param  array    An associative array of data to be filtered
     * @return array    The filtered data array
     */
    public function filter($data, $base = true)
    {
        settype($data, 'array'); //force to array
    
        // Filter out any extra columns.
        $data = array_intersect_key($data, $this->getColumns($base));
        
        // Filter data based on column type
        foreach($data as $key => $value) {
            $data[$key] = $this->getColumn($key, $base)->filter->sanitize($value);
        }
            
        return $data;
    }
     
	/**
	 * Search the behaviors to see if this table behaves as.
	 *
	 * Function is also capable of checking is a behavior has been mixed succesfully
	 * using is[Behavior] function. If the behavior exists the function will return 
	 * TRUE, otherwise FALSE.
	 *
	 * @param  string 	The function name
	 * @param  array  	The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
	 */
	public function __call($method, $arguments)
	{
		// If the method is of the form is[Bahavior] handle it.
		$parts = KInflector::explode($method);

		if($parts[0] == 'is' && isset($parts[1]))
		{
            if($this->hasBehavior(strtolower($parts[1]))) {
                 return true;    
            }
		        
			return false;
		}

		return parent::__call($method, $arguments);
	}	
}
