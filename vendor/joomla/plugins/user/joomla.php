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

		KService::get('repos:session.session')->destroy($event->user['id']);

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

		if (!$juser) {
			JError::raiseWarning(401, "Did not find a user with username: ".$juser['username']);
			return false;
		}

		// Register the needed session variables
		$session = KService::get('com:session');
		$session->set('user', $juser);

		if ($sessionEntity = KService::get('repos:session.session')->fetch(array('id' => $session->getId()))) {
			$sessionEntity->setData(array(
				'guest' => 0,
				'username' => $juser->get('username'),
				'userid' => (int) $juser->get('id'),
				'usertype' => $juser->get('usertype')
			))->save();
		}

		// Hit the user last visit field
		$juser->setLastVisit();

     	//cleanup session table from guest users
      	KService::get('repos:session.session')->destroy(0);

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

		if ($user['id'] == 0) {
			return false;
    	}

    	$viewer =& JFactory::getUser();

		//Check to see if we're deleting the current session
		if ($viewer->id == (int) $user['id']) {

			// Hit the user last visit field
			$viewer->setLastVisit();

			// Destroy the php session for this user
			$session = KService::get('com:session');
			$session->destroy();

			KService::get('repos:session.session')->destroy($user['id']);

			return true;
		}

		return false;
	}
}
