<?php

class ComPeopleSessionStorageDatabase extends ComPeopleSessionStorageAbstract
{
    private $_data = null;

	/**
	 * Open the SessionHandler backend.
	 *
	 * @access public
	 * @param string $save_path     The path to the session object.
	 * @param string $session_name  The name of the session.
	 * @return boolean  True on success, false otherwise.
	 */
	function open($save_path, $session_name)
	{
		return true;
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function close()
	{
		return true;
	}

 	/**
 	 * Read the data for a particular session identifier from the
 	 * SessionHandler backend.
 	 *
 	 * @access public
 	 * @param string $id  The session identifier.
 	 * @return string  The session data.
 	 */
	function read($id)
	{
		$db =& JFactory::getDBO();

		if (!$db->connected()) {
			return false;
		}

		$session =& JTable::getInstance('session');

        $session->load($id);

		return (string) $session->data;
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @access public
	 * @param string $id            The session identifier.
	 * @param string $session_data  The session data.
	 * @return boolean  True on success, false otherwise.
	 */
	function write($id, $session_data)
	{
		$db =& JFactory::getDBO();

		if (!$db->connected()) {
			return false;
		}

		$session = & JTable::getInstance('session');

        if ($session->load($id)) {
			$session->data = $session_data;
			$session->store();
		} else {
			$session->data = $session_data;
			$session->insert($id, 0);
		}

		return true;
	}

	/**
	  * Destroy the data for a particular session identifier in the
	  * SessionHandler backend.
	  *
	  * @access public
	  * @param string $id  The session identifier.
	  * @return boolean  True on success, false otherwise.
	  */
	function destroy($id)
	{
		$db =& JFactory::getDBO();

		if (!$db->connected()) {
			return false;
		}

		$session =& JTable::getInstance('session');
		$session->delete($id);

        return true;
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @access public
	 * @param integer $maxlifetime  The maximum age of a session.
	 * @return boolean  True on success, false otherwise.
	 */
	function gc($maxlifetime)
	{
		$db =& JFactory::getDBO();

		if(!$db->connected()) {
			return false;
		}

		$session =& JTable::getInstance('session');

        $session->purge($maxlifetime);

		return true;
	}
}
