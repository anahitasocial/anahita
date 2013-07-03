<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Store
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Database storage.
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Store
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainStoreDatabase extends KObject implements AnDomainStoreInterface
{        
	/**
	 * Resource columns
	 * 
	 * @var array
	 */
	protected $_columns = array();
	
	/**
	 * Database adapter  
	 * 
	 * @var object KDatabaseAbtract
	 */
	protected $_adapter;		
	
	/**
 	 * Map of native MySQL types to generic types used when reading
 	 * table column information.
 	 *
 	 * @var array
 	 */
 	protected $_typemap = array(

 	    // numeric
 	    'int'               => 'integer',
 	    'integer'           => 'integer',
 	    'bigint'            => 'integer',
 		'mediumint'			=> 'integer',
 		'smallint'			=> 'boolean',
 		'tinyint'			=> 'boolean',
 	    'numeric'			=> 'integer',
 	    'dec'               => 'float',
 	   	'decimal'           => 'float',
 	   	'float'				=> 'float'  ,
		'double'            => 'float'  ,
		'real' 				=> 'float'  ,
 	
 		// boolean
 		'bool'				=> 'boolean',
 		'boolean' 			=> 'boolean',

 	   	// date & time
 	   	'date'              => 'date'     ,
 	   	'time'              => 'date'     ,
 	   	'datetime'          => 'date',
 	   	'timestamp'         => 'date'  ,
 	   	'year'				=> 'integer'  ,
 	
 		//other
 		'set'				=> 'string',
 		'enum'				=> 'string', 	
	);

 	/**
 	 * Constructor.
 	 *
 	 * @param KConfig $config An optional KConfig object with configuration options.
 	 *
 	 * @return void
 	 */
 	public function __construct(KConfig $config)
 	{
 	    parent::__construct($config);
 	    
 	    $this->_adapter = $config->adapter;
 	    
 	    if ( !$this->_adapter) {
 	       throw new AnDomainStoreException('adapter [KDatabaseAdapterInterface] is required option');
 	    }
 	    
 	    //Set the mixer in the config
 	    $config->mixer = $this;
 	    
 	    $this->mixin( new KMixinCommand($config) );
 	}
 	    
 	 	
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(			    	
    		'adapter'           => null,
    		'command_chain'     => $this->getService('koowa:command.chain'),
    		'dispatch_events'   => true,
    		'event_dispatcher'  => $this->getService('koowa:event.dispatcher'),
    		'enable_callbacks' 	=> false
		));
			
		parent::_initialize($config);
	}
	
	/**
	 * Fetch a result from a store
	 *
	 * @param AnDomainQuery $query Query object
	 * @param int           $mode  Fetch Mode
	 * 
	 * @return mixed
	 */
	public function fetch($query, $mode)
	{
        $context = $this->getCommandContext();
        $context->mode  = $mode;
		$context->query = $query;
        $context->repository = $query->getRepository();
		
		if($this->getCommandChain()->run('before.fetch', $context) !== false)
		{
		    $modes = array(
		            AnDomain::FETCH_ROW 	    	=> KDatabase::FETCH_ARRAY,
		            AnDomain::FETCH_ROW_LIST   		=> KDatabase::FETCH_ARRAY_LIST,
		            AnDomain::FETCH_ENTITY			=> KDatabase::FETCH_ARRAY,
		            AnDomain::FETCH_ENTITY_SET		=> KDatabase::FETCH_ARRAY_LIST,
		            AnDomain::FETCH_ENTITY_LIST		=> KDatabase::FETCH_ARRAY_LIST,
		            AnDomain::FETCH_VALUE			=> KDatabase::FETCH_FIELD,
		            AnDomain::FETCH_VALUE_LIST		=> KDatabase::FETCH_FIELD_LIST
		    );
		    
		    $mode = $modes[$mode];

		    $context['data'] = $this->_adapter->select(to_str($query), $mode);
		    
		    $this->getCommandChain()->run('after.fetch', $context);
		}
		
		return $context->data;
	}
		
	/**
	 * Inserts an entity into the persistant store. It will return the insertId
	 *
	 * @param  AnDomainRepositoryAbstract $repository
	 * @param  array $data
	 * @return int
	 */
	public function insert($repository, $data)
	{
        $context = $this->getCommandContext();
        
        $context->data       = $data;
        $context->repository = $repository;
        
        if($this->getCommandChain()->run('before.insert', $context) !== false)
        {                    
    		$vals 	   = array();
    
    		$this->_adapter->getConnection()->autocommit(FALSE);
    		
    		$resources = $repository->getResources();
    		
    		foreach($data as $column => $val)
    		{
    			$column    = $resources->getColumn($column);
    			$key	   = $column->resource->getAlias();
    			if ( !isset($vals[$key]) )
    				 $vals[$key] = array();
    			
    			$vals[$key][$this->quoteName($column->name)]  = $this->quoteValue($val);		
    		}
    		
    		$main_insert_id = null;
    		
    		foreach($resources as $resource) 
    		{
    			//check if therre now columns to be saved then probably a second empty
    			//table so lets create an empty record
    			$columns  = isset($vals[$resource->getAlias()]) ? array_keys($vals[$resource->getAlias()])   : array();
    			$values   = isset($vals[$resource->getAlias()]) ? array_values($vals[$resource->getAlias()]) : array();
    			
    			if ( $resource !== $resources->main() ) {
    				$link	   = $resource->getLink();
    				$columns[] =  $link->child;
    				$values[]  =  $main_insert_id;
    			}
    
    			$query = 'INSERT INTO '.$this->_adapter->getTablePrefix().$resource->getName()
    					 . '('.implode(', ', $columns).') VALUES ('.implode(', ', $values).')';
    					 
    			
    			$this->execute($query);
    			
    			if ( $resource === $resources->main() )
    				$main_insert_id = $this->_adapter->getInsertId();
    			
    		}
            
    		$this->_adapter->getConnection()->commit();
    		$this->_adapter->getConnection()->autocommit(TRUE);
            
            $context->result = $main_insert_id;
            
            $this->getCommandChain()->run('after.insert', $context);
        }
		
		return $context->result;
	}
	
	/**
	 * Updates the record identified by $keys in resources with the passed in data 
	 *
	 * @param  AnDomainRepositoryAbstract $repository
	 * @param  array $keys
	 * @param  array $data
	 * @return void
	 */
	public function update($repository, $keys, $data)
	{
        $context = $this->getCommandContext();
        
        $context->data       = $data;
        $context->repository = $repository;
        $context->keys       = $keys;
        $context->query      = AnDomainQuery::getInstance($repository, $keys)->update(KConfig::unbox($data));
                
        if($this->getCommandChain()->run('before.update', $context) !== false)
        {
            $context->result = $this->execute($context->query);
            $this->getCommandChain()->run('after.update', $context);
        }
		
		return $context->result;
	}
	
	/**
	 * Delete record identified by $keys in the $resources
	 *
	 * @param  AnDomainRepositoryAbstract $repository
	 * @param  array $keys
	 * @return boolean
	 */
	public function delete($repository, $keys)
	{        
        $context = $this->getCommandContext();
        
        $context->repository = $repository;
        $context->keys       = $keys;
        $context->query      = AnDomainQuery::getInstance($repository, $keys)->delete();
                
        if($this->getCommandChain()->run('before.delete', $context) !== false)
        {
            $context->result = $this->execute($context->query);
            $this->getCommandChain()->run('after.delete', $context);
        }
        
        return $context->result;
	}
		
	/**
	 * Executes a query. The execute is ran through the  command chain
	 *
	 * @param  string $query
	 * @return boolean
	 */
	public function execute($query)
	{
		$context = $this->getCommandContext();
		
		$context->query = $query;
		
		if($this->getCommandChain()->run('before.execute', $context) !== false)
		{
			$context->result = $this->_adapter->execute(to_str($context->query));
			$this->getCommandChain()->run('after.execute', $context);
		}
		
		return $context->result;
	}
	
	/**
	 * Return an array of columns for a resource
	 * 
	 * @see    AnDomainStoreInterface::getColumns()
	 * @param  AnDomainResourceTable
	 * @return array
	 */
	public function getColumns($table)
	{
		if ( !isset($this->_columns[$table]) ) 
		{
			$fields  = $this->_adapter->select('SHOW COLUMNS FROM #__'.$table, KDatabase::FETCH_ARRAY_LIST);
			
			static 	$column;
			
			$columns = array();
			
			foreach($fields as $field) 
			{
				list($type, $length, $scope) = $this->_parseColumnType($field['Type']);							
				$column = $column ? clone $column : new AnDomainResourceColumn();
				$column->name		= $field['Field'];
				$column->type 		= isset($this->_typemap[$type]) ? $this->_typemap[$type] : 'string';
				$column->default    = $field['Default'];
                $column->required   = $field['Null'] == 'NO';
                $column->primary    = $field['Key'] == 'PRI';
                $column->unique     = $field['Key'] == 'UNI';
				$columns[$column->name]	= $column;
			}

			$this->_columns[$table] = $columns;	
		}
				
		return $this->_columns[$table];
	}
	
	/**
	 * Quote Value
	 *
	 * @param string $value The value to quote
	 * 
	 * @return mixed Return the quoted value
	 */
	public function quoteValue($value)
	{
        if ( $value === NULL )
			return 'NULL';
			
		if ( is_numeric($value) )
			return $value;
			
		if ( $value === false )
			return 0;
			
		if ( empty($value) )
			return '\'\'';

		if ( is_array($value) ) 
		{
			$values = array_unique($value);
			foreach($values as $key => $value) 
			{
			        $values[$key] = $value === NULL ? 'NULL' : $this->_adapter->quoteValue($value);
			}
			return $values = implode(', ', $values);
		}
					
		return $this->_adapter->quoteValue($value);
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
	    return $this->_adapter->quoteName($spec);
	}
	
    /**
	 * Given a raw column specification, parse into datatype, length, and decimal scope.
	 *
	 * @param string The column specification; for example,
 	 * "VARCHAR(255)" or "NUMERIC(10,2)" or ENUM('yes','no','maybe')
 	 *
 	 * @return array A sequential array of the column type, size, and scope.
 	 */
	protected function _parseColumnType($spec)
 	{
 	 	$spec    = strtolower($spec);
 	  	$type    = null;
 	   	$length  = null;
 	   	$scope   = null;

 	   	// find the parens, if any
 	   	$pos = strpos($spec, '(');
 	   	if ($pos === false)
 	   	{
 	     	// no parens, so no size or scope
 	      	$type = $spec;
 	   	}
 	   	else
 	   	{
 	   		// find the type first.
 	      	$type = substr($spec, 0, $pos);
 	      	
 	      	// there were parens, so there's at least a length
 	       	// remove parens to get the size.
 	      	$length = trim(substr($spec, $pos), '()');
 	      	
 	   		if($type != 'enum' && $type != 'set')
 	     	{
 	     		// A comma in the size indicates a scope.
 	      		$pos = strpos($length, ',');
 	      		if ($pos !== false) {
 	        		$scope = substr($length, $pos + 1);
 	           		$length  = substr($length, 0, $pos);
 	       		}
 	     		
 	     		
 	     	}
 	     	else $length = explode(',', str_replace("'", "", $length));
 	   	}
	 	
 	  	return array($type, $length, $scope);
 	}	
}