<?php

class AnDatabase extends KDatabaseAdapterMysqli implements KServiceInstantiatable
{
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
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * Recognized key values include 'command_chain', 'charset', 'table_prefix',
	 * (this list is not meant to be comprehensive).
	 */
	public function __construct( KConfig $config = null )
	{
		$this->_connected = (bool) $config->connected;
		parent::__construct($config);
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
        $settings = $this->getService('com:settings.setting');

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
		}

        if(!($db = new mysqli($host, $user, $password, NULL, $port, $socket))) {
            throw new Exception("Couldn't connect to the database!");
        }

        if (!$db->select_db($database)) {
            throw new Exception("The database \"$database\" doesn't seem to exist!");
		}

		$db->set_charset("utf8mb4");

		if (defined('MYSQLI_OPT_INT_AND_FLOAT_NATIVE')) {
			$db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
		}

        $config->append(array(
    		'connection' => $db,
            'table_prefix' => $settings->dbprefix,
			'charset' => 'utf8mb4',
			'connected' => true
        ));

        parent::_initialize($config);
    }
}
