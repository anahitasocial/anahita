<?php
/**
 * @version		$Id: user.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla.Framework
 * @subpackage	User
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport( 'joomla.html.parameter');


/**
 * User class.  Handles all application interaction with a user
 *
 * @package 	Joomla.Framework
 * @subpackage	User
 * @since		1.5
 */
class JUser extends JObject
{
	/**
	 * Unique id
	 * @var int
	 */
	var $id				= null;

	/**
	 * The users real name (or nickname)
	 * @var string
	 */
	var $name			= null;

	/**
	 * The login name
	 * @var string
	 */
	var $username		= null;

	/**
	 * The email
	 * @var string
	 */
	var $email			= null;

	/**
	 * MD5 encrypted password
	 * @var string
	 */
	var $password		= null;

	/**
	 * Clear password, only available when a new password is set for a user
	 * @var string
	 */
	var $password_clear	= '';

	/**
	 * Description
	 * @var string
	 */
	var $usertype		= null;

	/**
	 * Description
	 * @var int
	 */
	var $block			= null;

	/**
	 * Description
	 * @var datetime
	 */
	var $registerDate	= null;

	/**
	 * Description
	 * @var datetime
	 */
	var $lastvisitDate	= null;

	/**
	 * Description
	 * @var string activation hash
	 */
	var $activation		= null;

	/**
	 * Description
	 * @var boolean
	 */
	var $guest     = null;

	/**
	 * Error message
	 * @var string
	 */
	var $_errorMsg	= null;


	/**
	* Constructor activating the default information of the language
	*
	* @access 	protected
	*/
	function __construct($identifier = 0)
	{

		// Load the user if it exists
		if (!empty($identifier)) {
			$this->load($identifier);
		}
		else
		{
			//initialise
			$this->id        = 0;
			$this->guest     = 1;
		}
	}

	/**
	 * Returns a reference to the global User object, only creating it if it
	 * doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $user =& JUser::getInstance($id);</pre>
	 *
	 * @access 	public
	 * @param 	int 	$id 	The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 * @return 	JUser  			The User object.
	 * @since 	1.5
	 */
	public static function &getInstance($id = 0)
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		// Find the user id
		if(!is_numeric($id))
		{
			jimport('joomla.user.helper');
			if (!$id = JUserHelper::getUserId($id)) {
				JError::raiseWarning( 'SOME_ERROR_CODE', 'JUser::_load: User '.$id.' does not exist' );
				$retval = false;
				return $retval;
			}
		}

		if (empty($instances[$id])) {
			$user = new JUser($id);
			$instances[$id] = $user;
		}

		return $instances[$id];
	}

	/**
	 * Pass through method to the table for setting the last visit date
	 *
	 * @access 	public
	 * @param	int		$timestamp	The timestamp, defaults to 'now'
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function setLastVisit($timestamp=null)
	{
		// Create the user table object
		$table 	=& $this->getTable();
		$table->load($this->id);

		return $table->setLastVisit($timestamp);
	}

	/**
	 * Method to get the user table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access 	public
	 * @param	string	The user table name to be used
	 * @param	string	The user table prefix to be used
	 * @return	object	The user table object
	 * @since	1.5
	 */
	function &getTable( $type = null, $prefix = 'JTable' )
	{
		static $tabletype;

		//Set the default tabletype;
		if(!isset($tabletype)) {
			$tabletype['name'] 		= 'user';
			$tabletype['prefix']	= 'JTable';
		}

		//Set a custom table type is defined
		if(isset($type)) {
			$tabletype['name'] 		= $type;
			$tabletype['prefix']	= $prefix;
		}

		// Create the user table object
		$table 	=& JTable::getInstance( $tabletype['name'], $tabletype['prefix'] );
		return $table;
	}

	/**
	 * Method to bind an associative array of data to a user object
	 *
	 * @access 	public
	 * @param 	array 	$array 	The associative array to bind to the object
	 * @return 	boolean 		True on success
	 * @since 1.5
	 */
	function bind(& $array)
	{
		jimport('joomla.user.helper');

		// Lets check to see if the user is new or not
		if (empty($this->id))
		{
			// Check the password and create the crypted password
			if (empty($array['password'])) {
				$array['password']  = JUserHelper::genRandomPassword();
				$array['password2'] = $array['password'];
			}

			if ($array['password'] != $array['password2']) {
					$this->setError( AnTranslator::_( 'PASSWORD DO NOT MATCH.' ) );
					return false;
			}

			$this->password_clear = JArrayHelper::getValue( $array, 'password', '', 'string' );

			$salt  = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword($array['password'], $salt);
			$array['password'] = $crypt.':'.$salt;

			// Set the registration timestamp

			$now =& JFactory::getDate();
			$this->set( 'registerDate', $now->toMySQL() );

			// Check that username is not greater than 150 characters
			$username = $this->get( 'username' );
			if ( strlen($username) > 150 )
			{
				$username = substr( $username, 0, 150 );
				$this->set( 'username', $username );
			}

			// Check that password is not greater than 100 characters
			$password = $this->get( 'password' );
			if ( strlen($password) > 100 )
			{
				$password = substr( $password, 0, 100 );
				$this->set('password', $password );
			}
		}
		else
		{
			// Updating an existing user
			if (!empty($array['password']))
			{
				if ( $array['password'] != $array['password2'] ) {
					$this->setError( AnTranslator::_( 'PASSWORD DO NOT MATCH.' ) );
					return false;
				}

				$this->password_clear = JArrayHelper::getValue( $array, 'password', '', 'string' );

				$salt = JUserHelper::genRandomPassword(32);
				$crypt = JUserHelper::getCryptedPassword($array['password'], $salt);
				$array['password'] = $crypt.':'.$salt;
			}
			else
			{
				$array['password'] = $this->password;
			}
		}

		// TODO: this will be deprecated as of the ACL implementation
		$db =& JFactory::getDBO();

		// Bind the array
		if (!$this->setProperties($array)) {
			$this->setError("Unable to bind array to user object");
			return false;
		}

		// Make sure its an integer
		$this->id = (int) $this->id;

		return true;
	}

	/**
	 * Method to save the JUser object to the database
	 *
	 * @access 	public
	 * @param 	boolean $updateOnly Save the object only if not a new user
	 * @return 	boolean 			True on success
	 * @since 1.5
	 */
	function save( $updateOnly = false )
	{
		// Create the user table object
		$table 	=& $this->getTable();
		$table->bind($this->getProperties());

		// Check and store the object.
		if(!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

		//are we creating a new user
		$isnew = !$this->id;

		// If we aren't allowed to create new users return
		if($isnew && $updateOnly)
			return false;

		// Get the old user
		$old = new JUser($this->id);

		dispatch_plugin('user.onBeforeStoreUser', array(
			'user' => $old->getProperties(),
			'isNew' => $isnew
		));

		//Store the user data in the database
		if (!$result = $table->store()) {
			$this->setError($table->getError());
		}

		// Set the id for the JUser object in case we created a new user.
		if (empty($this->id)) {
			$this->id = $table->get( 'id' );
		}

		//Fire the onAftereStoreUser event
		dispatch_plugin('user.onAfterStoreUser', array(
			'user' => $this->getProperties(),
			'isNew' => $isnew,
			'result' => $result,
			'message' => $this->getError()
		));

		return $result;
	}

	/**
	 * Method to delete the JUser object from the database
	 *
	 * @access 	public
	 * @param 	boolean $updateOnly Save the object only if not a new user
	 * @return 	boolean 			True on success
	 * @since 1.5
	 */
	function delete()
	{

		dispatch_plugin('user.onBeforeDeleteUser', array(
			'user' => $this->getProperties()
		));

		// Create the user table object
		$table 	=& $this->getTable();

		$result = false;
		if (!$result = $table->delete($this->id)) {
			$this->setError($table->getError());
		}

		//trigger the onAfterDeleteUser event
		dispatch_plugin('user.onAfterDeleteUser', array(
			'user' => $this->getProperties(),
			'result' => $result,
			'message' => $this->getError()
		));

		return $result;

	}

	/**
	 * Method to load a JUser object by user id number
	 *
	 * @access 	public
	 * @param 	mixed 	$identifier The user id of the user to load
	 * @param 	string 	$path 		Path to a parameters xml file
	 * @return 	boolean 			True on success
	 * @since 1.5
	 */
	function load($id)
	{
		// Create the user table object
		$table 	=& $this->getTable();

		 // Load the JUserModel object based on the user id or throw a warning.
		 if(!$table->load($id)) {
			JError::raiseWarning( 'SOME_ERROR_CODE', 'JUser::_load: Unable to load user with id: '.$id );
			return false;
		}

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());

		return true;
	}
}
