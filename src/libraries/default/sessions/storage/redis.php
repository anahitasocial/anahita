<?php

class LibSessionsStorageRedis extends LibSessionsStorageAbstract
{
    /**
    * @param hold session data
    */
    protected $_data = null;

    /**
    * @param $session entity
    */
    protected $_session = null;

    /**
    * @param redis session repository
    */
    protected $_redis = null;

    /**
    * loads the session entity
    *
    * @param $id string session id
    * @return session entity
    */
    private function _getSession($sessionId)
    {
        if (is_null($this->_session)) {
            $this->_session = $this->_redis->get($sessionId);
        }

        return $this->_session;
    }

    /**
	 * Open the SessionHandler backend.
	 *
	 * @abstract
	 * @access public
	 * @param string $save_path     The path to the session object.
	 * @param string $session_name  The name of the session.
	 * @return boolean  True on success, false otherwise.
	 */
	public function open($save_path, $session_name)
	{
        $this->_redis = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port' => 6379,
        ]);

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
	public function read($id)
	{
        $this->_data = '';

        if ($id === '') {
            return $this->_data;
        }

        if ($session = $this->_getSession($id)) {
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

        $result = false;
        $session = $this->_getSession($id);

        if (isset($session)) {
            $result = $session->set('meta', $session_data)->save();
        } else {
            $result = $this->_redis->set($id, $session_data);
        }

        if ($result) {
            $this->_data = $session_data;
        }

		return $result;
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
            return $this->_redis->delete($id);
        }

        return false;
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
        // return $this->_redis->purge($lifetime);
	}
}
