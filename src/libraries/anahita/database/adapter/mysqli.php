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

            $settings = new JConfig();

    		if ($settings->caching) {
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
        $settings = new JConfig();

        $database = $settings->db;
        $prefix = $settings->dbprefix;
        $port = NULL;
		$socket	= NULL;
        $host = $settings->host;
        $user = $settings->user;
        $password = $settings->password;

		$targetSlot = substr(strstr($host, ":"), 1);

        if (!empty($targetSlot)) {

			// Get the port number or socket name
			if (is_numeric($targetSlot)) {
                $port = $targetSlot;
            } else {
				$socket	= $targetSlot;
            }

			// Extract the host name only
			$host = substr($host, 0, strlen($host) - (strlen($targetSlot) + 1));

            // This will take care of the following notation: ":3306"
			if($host === '') {
				$host = 'localhost';
            }
		}

        //test to see if driver exists
        if (!function_exists( 'mysqli_connect' )) {
            throw new Exception('The MySQL adapter "mysqli" is not available!');
            return;
		}

        if(!($db = new mysqli($host, $user, $password, NULL, $port, $socket))) {
            throw new Exception("Couldn't connect to the database!");
			return false;
        }

        if (!$db->select_db($database)) {
            throw new Exception("The database \"$database\" doesn't seem to exist!");
			return false;
		}

        $config->append(array(
    		'connection' => $db,
            'table_prefix' => $settings->dbprefix,
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
	    if (!isset($this->_table_schema[$table]) && isset($this->_cache)) {

            $database = $this->getDatabase();
		    $identifier = md5($database.$table);

	        if (!$schema = $this->_cache->get($identifier)) {
                $schema = parent::getTableSchema($table);
		   	    $this->_cache->store(serialize($schema), $identifier);
	        } else {
                $schema = unserialize($schema);
            }

		    $this->_table_schema[$table] = $schema;
	    }

	    return parent::getTableSchema($table);
	}
}
