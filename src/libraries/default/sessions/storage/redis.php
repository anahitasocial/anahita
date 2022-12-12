<?php

class LibSessionsStorageRedis extends LibSessionsStorageAbstract
{
    /**
    * @param $session entity
    */
    protected $_session = null;

	/**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
		$client = new Predis\Client($config->host, array(
			'parameters' => array(
				'password' => $config->password,
			),
		));
		
        $this->_session = new Predis\Session\Handler($client, array('gc_maxlifetime' => $config->expire));
        
        parent::__construct($config);
    }

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional AnConfig object with configuration options.
     * @return  void
     */
	protected function _initialize(AnConfig $config)
    {
		$settings = $this->getService('com:settings.config');
		
		$config->append(array(
			'expire' => 15,
			'host' => $settings->redis_host,
			'password' => $settings->redis_password,
		));

		parent::_initialize($config);
	}

    /**
     * Registers this instance as the current session handler.
     */
    public function register()
    {
        $this->_session->register();
    }

    /**
	 * Open the SessionHandler backend.
	 *
	 * @abstract
	 * @access public
	 * @param string $save_path     The path to the session object.
	 * @param string $session_name  The name of the session.
	 * @return boolean  True on success, false otherwise.
	 */
	public function open($save_path, $session_id)
	{
        return $this->_session->open($save_path, $session_id);
	}

 	/**
 	 * Read the data for a particular session identifier from the
 	 * SessionHandler backend.
 	 *
 	 * @access public
 	 * @param string $id  The session identifier.
 	 * @return string  The session data.
 	 */
	public function read($id)
	{
		return $this->_session->read($id);
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @access public
	 * @param string $id            The session identifier.
	 * @param string $session_data  The session data.
	 * @return boolean  True on success, false otherwise.
	 */
	public function write($session_id, $session_data)
	{
        return $this->_session->write($session_id, $session_data);
	}

	/**
	  * Destroy the data for a particular session identifier in the
	  * SessionHandler backend.
	  *
	  * @access public
	  * @param string $session_id  The session identifier.
	  * @return boolean  True on success, false otherwise.
	  */
	public function destroy($session_id)
	{
        return $this->_session->destroy($session_id);
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @access public
	 * @param integer $maxlifetime  The maximum age of a session. 60 days by default
	 * @return boolean  True on success, false otherwise.
	 */
	public function gc($lifetime)
	{
        return $this->_session->gc($lifetime);
	}
}
