<?php

class LibSessionsStorageDatabase extends LibSessionsStorageAbstract
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
        $this->_data = '';

        $session = KService::get('repos:sessions.session')
        ->getQuery()
        ->fetch(array('id' => $id));

        if (isset($session)) {
            $this->_data = (string) $session->meta;
        }

		return $this->_data;
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @access public
	 * @param string $id            The session identifier.
	 * @param string $session_data  The session data.
	 * @return boolean  True on success, false otherwise.
	 */
	function write($id = '', $meta = '')
	{
        $session = KService::get('repos:sessions.session')
        ->getQuery()
        ->fetch(array('id' => $id));

        if(isset($session)) {

            $session
            ->set('id', $id)
            ->set('meta', $meta)
            ->save();

            $this->_data = $meta;

        } else {

            $session = KService::get('repos:sessions.session')->getEntity();
            $session
            ->set('id', $id)
            ->set('meta', '')
            ->save();

            $this->_data = '';
        }

		return true;
	}

    function update($id) {

        $session = KService::get('repos:sessions.session')
        ->getQuery()
        ->fetch(array('id' => $id));

        if(isset($session)) {
            return $session->set('time', time())->save();
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
        $session = KService::get('repos:sessions.session')
        ->getQuery()
        ->fetch(array('id' => $id));

        if (isset($session)) {
		    $session->delete();
        }

        return true;
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @access public
	 * @param integer $maxlifetime  The maximum age of a session. 60 days by default
	 * @return boolean  True on success, false otherwise.
	 */
	function gc($lifetime = LibSessionsDomainEntitySession::MAX_LIFETIME)
	{
        KService::get('repos:sessions.session')->purge($lifetime);

		return true;
	}
}
