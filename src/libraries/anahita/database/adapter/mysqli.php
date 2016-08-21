<?php

class AnDatabaseAdapterMysqli extends KDatabaseAdapterMysqli implements KServiceInstantiatable
{
    /**
  	 * The cache object
  	 *
  	 * @var	JCache
  	 */
      protected $_cache;

  	/**
  	 * Constructor
  	 *
  	 * Prevent creating instances of this class by making the contructor private
  	 *
  	 * @param 	object 	An optional KConfig object with configuration options
  	 */
  	public function __construct(KConfig $config)
  	{
    		parent::__construct($config);

    		if (JFactory::getConfig()->getValue('config.caching')) {
    	     $this->_cache = JFactory::getCache('database', 'output');
    		}
  	}

	/**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return KDatabaseTableInterface
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
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
        $db = JFactory::getDBO();

		    $resource = method_exists($db, 'getConnection') ? $db->getConnection() : $db->_resource;
		    $prefix   = method_exists($db, 'getPrefix') ? $db->getPrefix() : $db->_table_prefix;

        $config->append(array(
    		'connection'   => $resource,
            'table_prefix' => $prefix,
        ));

        parent::_initialize($config);
    }

	/**
	 * Retrieves the table schema information about the given table
	 *
	 * This function try to get the table schema from the cache. If it cannot be found
	 * the table schema will be retrieved from the database and stored in the cache.
	 *
	 * @param 	string 	A table name or a list of table names
	 * @return	KDatabaseSchemaTable
	 */
	public function getTableSchema($table)
	{
	    if(!isset($this->_table_schema[$table]) && isset($this->_cache))
		{
		    $database = $this->getDatabase();

		    $identifier = md5($database.$table);

	        if (!$schema = $this->_cache->get($identifier))
	        {
	            $schema = parent::getTableSchema($table);

	            //Store the object in the cache
		   	    $this->_cache->store(serialize($schema), $identifier);
	        }
	        else $schema = unserialize($schema);

		    $this->_table_schema[$table] = $schema;
	    }

	    return parent::getTableSchema($table);
	}
}
