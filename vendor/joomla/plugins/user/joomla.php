<?php
/**
* @version		$Id: joomla.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	JFramework
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * Joomla User plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgUserJoomla extends JPlugin
{

	/**
	 * Remove all sessions for the user name
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param 	array	  	holds the user data
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	public function onAfterDeleteUser($user, $succes, $msg)
	{
		if (! $succes) 
		{
			return false;
		}

		$db =& JFactory::getDBO();
		$db->setQuery('DELETE FROM #__session WHERE userid = '.$db->Quote($user['id']));
		$db->Query();

		return true;
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @access	public
	 * @param   array   holds the user data
	 * @param 	array   array holding options (remember, autoregister, group)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function onLoginUser($user, $options = array())
	{
		global $mainframe;
        
		jimport('joomla.user.helper');
        $viewer =& JFactory::getUser($user['username']);

        if (! $viewer->id)
        {
            return JError::raiseWarning(401, "Did not find a user with username: ".$user['username']);
        }
        
        if ( 
            $mainframe->isAdmin() && 
            $viewer->usertype != ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR && 
            $viewer->usertype != ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR  
        )   
        {    
            return JError::raiseWarning(403, "Not authorized to access the admin side");
        }
        
        if ($viewer->block == 1)
        {
            return JError::raiseWarning(403, "Not authorized to access this site");
            return false;
        }

		// Register the needed session variables
		$session =& JFactory::getSession();		
		$session->set('user', $viewer);		
		
		// Get the session object
		$table = & JTable::getInstance('session');
		$table->load($session->getId());

		$table->guest = $viewer->get('guest');
		$table->username = $viewer->get('username');
		$table->userid 	= intval($viewer->get('id'));
		$table->usertype = $viewer->get('usertype');
		
		$table->update();		
		
		// Hit the user last visit field
		$viewer->setLastVisit();
        
        //cleanup session table from guest users
        $db =& JFactory::getDBO();
        $db->setQuery('DELETE FROM #__session WHERE userid = 0 ');
        $db->Query();

		return true;
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param  array	holds the user data
	 * @param 	array   array holding options (client, ...)
	 * @return object   True on success
	 * @since 1.5
	 */
	public function onLogoutUser($user, $options = array())
	{
		if ($user['id'] == 0)
        { 
			return false;
        }
        
        $viewer =& JFactory::getUser();
        
		//Check to see if we're deleting the current session
		if ($viewer->id == (int) $user['id'])
		{
			// Hit the user last visit field
			$viewer->setLastVisit();

			// Destroy the php session for this user
			$session =& JFactory::getSession();
			$session->destroy();
		} 
		else 
		{
			// Force logout all users with that userid
			$table = & JTable::getInstance('session');
			$table->destroy((int) $user['id'], (int) $options['clientid']);
		}
        
		return true;
	}
}
