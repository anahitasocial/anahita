<?php

/**
 * Joomla User plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgUserJoomla extends PlgAnahitaDefault
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
	public function onAfterDeleteUser(KEvent $event)
	{
		if(!$event->succes) {
			return false;
		}

		$db =& JFactory::getDBO();
		$db->setQuery('DELETE FROM #__session WHERE userid = '.$db->Quote($event->user['id']));
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
	public function onLoginUser(KEvent $event)
	{
			$user = $event->user;
			$options = $event->options;

			global $mainframe;

			jimport('joomla.user.helper');
		  $juser =& JFactory::getUser($user['username']);

		  if (!$juser){
		      JError::raiseWarning(401, "Did not find a user with username: ".$juser['username']);

					return false;
		  }

			// Register the needed session variables
			$session =& JFactory::getSession();
			$session->set('user', $juser);

			// Get the session object
			$table = & JTable::getInstance('session');
			if($table->load($session->getId()))
			{
					$table->guest = 0;
					$table->username = $juser->get('username');
					$table->userid = intval($juser->get('id'));
					$table->usertype = $juser->get('usertype');
					$table->update();
			}

			// Hit the user last visit field
			$juser->setLastVisit();

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
	public function onLogoutUser(KEvent $event)
	{
		$user = $event->user;
		$options = $event->options;

		if ($user['id'] == 0) {
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
