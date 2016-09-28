<?php

class LibSessionsStorageDatabase extends LibSessionsStorageAbstract
{
    /**
    * @param hold session data
    */
    private $_data = null;

    /**
    * @param $session entity
    */
    private $_session = null;

    /**
    * loads the session entity
    *
    * @param $id string session id
    * @return session entity
    */
    private function _getSession($id)
    {
        if (is_null($this->_session)) {
            $this->_session = KService::get('repos:sessions.session')->find(array('id' => $id));
        }

        return $this->_session;
    }

 	/**
 	 * Read the data for a particular session identifier from the
 	 * SessionHandler backend.
 	 *
 	 * @access public
 	 * @param string $id  The session identifier.
 	 * @return string  The session data.
 	 */
	public function read($id)
	{
        $this->_data = '';

        if ($id == '') {
            return $this->_data;
        }

        $session = $this->_getSession($id);

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
	public function write($id, $session_data)
	{
        if ($id === '' && $session_data === '') {
            return false;
        }

        //error_log('Writing session: ' . $id);

        $session = $this->_getSession($id);

        if (isset($session)) {
            $session->set('meta', $session_data);
        } else {

            KService::get('repos:sessions.session')
            ->getEntity()
            ->set('id', $id)
            ->set('meta', $session_data)
            ->save();
        }

        $this->_data = $session_data;

		return true;
	}

    public function update($id) {

        if ($id == '') {
            return false;
        }

        $session = $this->_getSession($id);

        if(isset($session)) {

            $viewer = get_viewer();

            $session
            ->set('time', time())
            ->set('nodeId', $viewer->id)
            ->set('username', $viewer->username)
            ->set('usertype', $viewer->usertype)
            ->set('guest', $viewer->guest());
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
	public function destroy($id)
	{
        if ($session = $this->_getSession($id)) {
            KService::get('repos:sessions.session')->destroy($session->nodeId);
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
	public function gc($lifetime = LibSessionsDomainEntitySession::MAX_LIFETIME)
	{
        KService::get('repos:sessions.session')->purge($lifetime);
		return true;
	}
}
