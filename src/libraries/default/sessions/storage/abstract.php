<?php

class LibSessionsStorageAbstract extends KObject implements SessionHandlerInterface, KServiceInstantiatable
{
	/**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
		parent::__construct($config);
		$this->register();
	}

	/**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfigInterface object with configuration options
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
	* Register the functions of this class with PHP's session handler
	*
	*/
	public function register()
	{
		session_set_save_handler(
			array($this, 'open'),
			array($this, 'close'),
			array($this, 'read'),
			array($this, 'write'),
			array($this, 'destroy'),
			array($this, 'gc')
		);
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
	public function open($save_path, $session_name)
	{
		return true;
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @abstract
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	public function close()
	{
		return true;
	}

 	/**
 	 * Read the data for a particular session identifier from the
 	 * SessionHandler backend.
 	 *
 	 * @abstract
 	 * @access public
 	 * @param string $id  The session identifier.
 	 * @return string  The session data.
 	 */
	public function read($id)
	{
		return;
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @abstract
	 * @access public
	 * @param string $id            The session identifier.
	 * @param string $session_data  The session data.
	 * @return boolean  True on success, false otherwise.
	 */
	public function write($id, $session_data)
	{
		return true;
	}

	/**
	  * Destroy the data for a particular session identifier in the
	  * SessionHandler backend.
	  *
	  * @abstract
	  * @access public
	  * @param string $id  The session identifier.
	  * @return boolean  True on success, false otherwise.
	  */
	public function destroy($id)
	{
		return true;
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @abstract
	 * @access public
	 * @param integer $maxlifetime  The maximum age of a session.
	 * @return boolean  True on success, false otherwise.
	 */
	public function gc($maxlifetime)
	{
		return true;
	}

	/**
	 * Test to see if the SessionHandler is available.
	 *
	 * @abstract
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	public function test()
	{
		return true;
	}
}
