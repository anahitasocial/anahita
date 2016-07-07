<?php
/**
* @version		$Id: application.php 21074 2011-04-04 16:51:40Z dextercowley $
* @package		Joomla.Framework
* @subpackage	Application
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.event.dispatcher');

/**
* Base class for a Joomla! application.
*
* Acts as a Factory class for application specific objects and provides many
* supporting API functions. Derived clases should supply the route(), dispatch()
* and render() functions.
*
* @abstract
* @package		Joomla.Framework
* @subpackage	Application
* @since		1.5
*/

class JApplication extends JObject
{

	/**
	 * The name of the application
	 *
	 * @var		array
	 * @access	protected
	 */
	var $_name = null;

	/**
	 * The scope of the application
	 *
	 * @var		string
	 * @access	public
	 */
	var $scope = null;

	/**
	* Class constructor.
	*
	* @param	integer	A client identifier.
	*/
	function __construct($config = array())
	{
		jimport('joomla.utilities.utility');

		//set the view name
		$this->_name		= $this->getName();

		//Enable sessions by default
		if(!isset($config['session'])) {
			$config['session'] = true;
		}

		//Set the session default name
		if(!isset($config['session_name'])) {
			 $config['session_name'] = $this->_name;
		}

		//Set the default configuration file
		if(!isset($config['config_file'])) {
			$config['config_file'] = 'configuration.php';
		}

		//create the configuration object
		$this->_createConfiguration(JPATH_CONFIGURATION.DS.$config['config_file']);

		//create the session if a session name is passed
		if($config['session'] !== false) {
			$this->_createSession(JUtility::getHash($config['session_name']));
		}

		$this->set( 'requestTime', gmdate('Y-m-d H:i') );
	}

	/**
	 * Returns a reference to the global JApplication object, only creating it if it
	 * doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $menu = &JApplication::getInstance();</pre>
	 *
	 * @access	public
	 * @param	mixed	$id 		A client identifier or name.
	 * @param	array	$config 	An optional associative array of configuration settings.
	 * @return	JApplication	The appliction object.
	 * @since	1.5
	 */
	static public function &getInstance($client, $config = array(), $prefix = 'J')
	{
			static $instances;

			if (!isset( $instances )) {
				$instances = array();
			}

			if (empty($instances[$client]))
			{
	        $classname = $prefix.ucfirst($client);

	        if ( !class_exists($classname) ) {
	            $error = JError::raiseError(500, 'Unable to load application: '.$client);
	            return $error;
	        }

	        $instance = new $classname($config);

					$instances[$client] =& $instance;
			}

			return $instances[$client];
	}

	/**
	* Initialise the application.
	*
	* @param	array An optional associative array of configuration settings.
	* @access	public
	*/
	function initialise($options = array())
	{
		//Set the language in the class
		$config =& JFactory::getConfig();

		// Check that we were given a language in the array (since by default may be blank)
		if(isset($options['language'])) {
			$config->setValue('config.language', $options['language']);
		}
	}

	 /**
	 * Gets a configuration value.
	 *
	 * @access	public
	 * @param	string	The name of the value to get.
	 * @return	mixed	The user state.
	 * @example	application/japplication-getcfg.php Getting a configuration value
	 */
	function getCfg( $varname )
	{
		$config =& JFactory::getConfig();
		return $config->getValue('config.' . $varname);
	}

	/**
	 * Method to get the application name
	 *
	 * The dispatcher name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @access	public
	 * @return	string The name of the dispatcher
	 * @since	1.5
	 */
	function getName()
	{
		$name = $this->_name;

		if (empty( $name )) {

			$r = null;

			if (!preg_match( '/J(.*)/i', get_class( $this ), $r)) {
					JError::raiseError(500, "JApplication::getName() : Can\'t get or parse class name.");
			}

			$name = strtolower( $r[1] );
		}

		return $name;
	}

	/**
	 * Registers a handler to a particular event group.
	 *
	 * @static
	 * @param	string	The event name.
	 * @param	mixed	The handler, a function or an instance of a event object.
	 * @return	void
	 * @since	1.5
	 */
	function registerEvent($event, $handler)
	{
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->register($event, $handler);
	}

	/**
	 * Calls all handlers associated with an event group.
	 *
	 * @static
	 * @param	string	The event name.
	 * @param	array	An array of arguments.
	 * @return	array	An array of results from each function call.
	 * @since	1.5
	 */
	function triggerEvent($event, $args=null)
	{
		$dispatcher =& JDispatcher::getInstance();
		return $dispatcher->trigger($event, $args);
	}

	/**
	 * Create the configuration registry
	 *
	 * @access	private
	 * @param	string	$file 	The path to the configuration file
	 * return	JConfig
	 */
	function &_createConfiguration($file)
	{
		jimport( 'joomla.registry.registry' );

		require_once( $file );

		// Create the JConfig object
		$config = new JConfig();

		// Get the global configuration object
		$registry =& JFactory::getConfig();

		// Load the configuration values into the registry
		$registry->loadObject($config);

		return $config;
	}

	/**
	 * Create the user session.
	 *
	 * Old sessions are flushed based on the configuration value for the cookie
	 * lifetime. If an existing session, then the last access time is updated.
	 * If a new session, a session id is generated and a record is created in
	 * the #__sessions table.
	 *
	 * @access	private
	 * @param	string	The sessions name.
	 * @return	object	JSession on success. May call exit() on database error.
	 * @since	1.5
	 */
	function &_createSession( $name )
	{
		$options = array();
		$options['name'] = $name;
    $options['force_ssl'] = isSSL();

		$session =& JFactory::getSession($options);

		jimport('joomla.database.table');
		$storage = & JTable::getInstance('session');
		$storage->purge($session->getExpire());

		// Session exists and is not expired, update time in session table
		if ($storage->load($session->getId())) {
				$storage->update();
				return $session;
		}

		//Session doesn't exist yet, initalise and store it in the session table
		$session->set('registry',	new JRegistry('session'));
		$session->set('user',		new JUser());

		if (!$storage->insert( $session->getId(), 0)) {
				jexit( $storage->getError());
		}

		return $session;
	}
}
