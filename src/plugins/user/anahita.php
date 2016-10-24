<?php

class plgUserAnahita extends PlgAnahitaDefault
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
		$person = $event->person;

		if ($person->id === 0) {
			return false;
    	}

		KService::get('repos:sessions.session')->destroy(array('nodeId' => $person->id));

		return true;
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @access	public
	 * @param   array   holds the user data
	 * @param 	array   array holding options (remember, autoregister, group)
	 * @return	boolean	True on success
	 */
	public function onLoginUser(KEvent $event)
	{
		$credentials = $event->credentials;
		$options = $event->options;

		$person = KService::get('repos:people.person')->find(array(
						'username' => $credentials->username
					));

		if (!isset($person)) {
			$msg = "Did not find a user with username: ".$credentials->username;
			throw new AnErrorException($msg, KHttpResponse::UNAUTHORIZED);
			return false;
		}

		$person->visited();
		$session = KService::get('com:sessions');
		$session->set('person', (object) $person->getData());

		return true;
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param  array	holds the user data
	 * @param 	array   array holding options (client, ...)
	 * @return object   True on success
	 */
	public function onLogoutUser(KEvent $event)
	{
		$person = $event->person;

		if ($person->id === 0) {
			return false;
    	}

		KService::get('repos:sessions.session')->destroy(array('nodeId' => $person->id));

		return KService::get('com:sessions')->destroy();
	}
}
