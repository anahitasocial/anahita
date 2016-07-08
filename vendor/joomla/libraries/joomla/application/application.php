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

	/**
	 * Redirect to another URL.
	 *
	 * Optionally enqueues a message in the system message queue (which will be displayed
	 * the next time a page is loaded) using the enqueueMessage method. If the headers have
	 * not been sent the redirect will be accomplished using a "301 Moved Permanently" or "303 See Other"
	 * code in the header pointing to the new location depending upon the moved flag. If the headers
	 * have already been sent this will be accomplished using a JavaScript statement.
	 *
	 * @access	public
	 * @param	string	$url	The URL to redirect to. Can only be http/https URL
	 * @param	string	$msg	An optional message to display on redirect.
	 * @param	string  $msgType An optional message type.
	 * @param	boolean	True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
	 * @return	none; calls exit().
	 * @since	1.5
	 * @see		JApplication::enqueueMessage()
	 */
	function redirect( $url, $msg='', $msgType='message', $moved = false )
	{
		// check for relative internal links
		if (preg_match( '#^index[2]?.php#', $url )) {
			$url = JURI::base() . $url;
		}

		// Strip out any line breaks
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		// If we don't start with a http we need to fix this before we proceed
		// We could validly start with something else (e.g. ftp), though this would
		// be unlikely and isn't supported by this API
		if(!preg_match( '#^http#i', $url )) {
			$uri =& JURI::getInstance();
			$prefix = $uri->toString(Array('scheme', 'user', 'pass', 'host', 'port'));
			if($url[0] == '/') {
				// we just need the prefix since we have a path relative to the root
				$url = $prefix . $url;
			} else {
				// its relative to where we are now, so lets add that
				$parts = explode('/', $uri->toString(Array('path')));
				array_pop($parts);
				$path = implode('/',$parts).'/';
				$url = $prefix . $path . $url;
			}
		}


		// If the message exists, enqueue it
		if (trim( $msg )) {
			$this->enqueueMessage($msg, $msgType);
		}

		// Persist messages if they exist
		if (count($this->_messageQueue))
		{
			$session =& JFactory::getSession();
			$session->set('application.queue', $this->_messageQueue);
		}

		// If the headers have been sent, then we cannot send an additional location header
		// so we will output a javascript redirect statement.
		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		}
		else
		{
			if (!$moved && strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'webkit') !== false) {
				// WebKit browser - Do not use 303, as it causes subresources reload (https://bugs.webkit.org/show_bug.cgi?id=38690)
				echo '<html><head><meta http-equiv="refresh" content="0;'. $url .'" /></head><body></body></html>';
			}
			else {
				// All other browsers, use the more efficient HTTP header method
				header($moved ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
				header('Location: '.$url);
			}
		}
		$this->close();
	}

	function close( $code = 0 ) {
			exit($code);
	}

	/**
	 * Enqueue a system message.
	 *
	 * @access	public
	 * @param	string 	$msg 	The message to enqueue.
	 * @param	string	$type	The message type.
	 * @return	void
	 * @since	1.5
	 */
	function enqueueMessage( $msg, $type = 'message' )
	{
		// For empty queue, if messages exists in the session, enqueue them first
		if (!count($this->_messageQueue))
		{
			$session =& JFactory::getSession();
			$sessionQueue = $session->get('application.queue');
			if (count($sessionQueue)) {
				$this->_messageQueue = $sessionQueue;
				$session->set('application.queue', null);
			}
		}
		// Enqueue the message
		$this->_messageQueue[] = array('message' => $msg, 'type' => strtolower($type));
	}

	/**
	 * Logout authentication function.
	 *
	 * Passed the current user information to the onLogoutUser event and reverts the current
	 * session record back to 'anonymous' parameters.
	 *
	  * @param 	int 	$userid   The user to load - Can be an integer or string - If string, it is converted to ID automatically
	 * @param	array 	$options  Array( 'clientid' => array of client id's )
	 *
	 * @access public
	 */
	function logout($userid = null, $options = array())
	{
		// Initialize variables
		$retval = false;

		// Get a user object from the JApplication
		$user = &JFactory::getUser($userid);

		// Build the credentials array
		$parameters['username']	= $user->get('username');
		$parameters['id']		= $user->get('id');

		// Set clientid in the options array if it hasn't been set already
		$options['clientid'] = 0;

		$results = dispatch_plugin('user.onLogoutUser', array(
									'user' => $parameters,
									'options' => $options
								));

		/*
		 * If any of the authentication plugins did not successfully complete
		 * the logout routine then the whole method fails.  Any errors raised
		 * should be done in the plugin as this provides the ability to provide
		 * much more information about why the routine may have failed.
		 */
		if (!$results) {
			setcookie( JUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, '/' );
			return true;
		}


		// Trigger onLoginFailure Event
		$this->triggerEvent('onLogoutFailure', array($parameters));

		return false;
	}
}
