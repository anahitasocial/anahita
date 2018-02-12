<?php

/**
 * Person Helper. Provides some helper functions suchs as creating a person object from a user.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleHelperPerson extends KObject
{
    /**
     * Logs in a user.
     *
     * @param array $user     The user as an array
     * @param bool  $remember Flag to whether remember the user or not
     *
     * @return bool
     */
    public function login(array $credentials, $remember = true)
    {
        $uniqueAlias = strpos($credentials['username'], '@') ? 'email' : 'username';
        $person = $this->getService('repos:people.person')->find(array(
            $uniqueAlias => $credentials['username'],
            'enabled' => 1
        ));

        if (is_null($person)) {
            $msg = "Did not find a user with username: ".$credentials['username'];
            throw new AnErrorException($msg, KHttpResponse::UNAUTHORIZED);
        } else {
            $person->visited();
            $this->_createSession($person);

            $results = dispatch_plugin('user.onAfterLoginPerson', array('person' => $person));
            foreach ($results as $result) {
                if ($result instanceof Exception || $result === false) {
                    return false;
                }
            }
        }

        // create a remember cookie that contains the ecrypted username and password
        if ($remember) {
            $key = get_hash('AN_LOGIN_REMEMBER', 'md5');
            $crypt = $this->getService('anahita:encrypter', array('key' => $key, 'cipher' => 'AES-256-CBC'));
            $cookie = $crypt->encrypt(serialize(array(
                'username' => $credentials['username'],
                'password' => $credentials['password'],
            )));
            $oneYear = 365 * 24 * 3600;
            $hash = get_hash('AN_LOGIN_REMEMBER');
            setcookie($hash, $cookie, time() + $oneYear, '/');
        }

        return $person;
    }

    /**
     *  Deletes a session and logs out the viewer.
     *
     *  @param object ComPeopleDomainEntityPerson
     *  @return bool
     */
    public function logout($person)
    {
        $this->_destroySession($person);

        $oneYear = 365 * 24 * 3600;
        $hash = get_hash('AN_LOGIN_REMEMBER');

        setcookie($hash, '', time() - $oneYear, '/');

        return true;
    }

    /**
    *   Creates a session for the given person
    *
    *   @param object ComPeopleDomainEntityPerson
    *   @return void
    */
    private function _createSession(ComPeopleDomainEntityPerson $person)
    {
        $session = $this->getService('com:application', array('session' => true))->getSession();

        $repo = $this->getService('repos:sessions.session');
        $entity = $repo->findOrAddNew(array('sessionId' => $session->getId()));
        $entity->setData(array(
            'nodeId' => (int) $person->id,
            'username' => (string) $person->username,
            'usertype' => (string) $person->usertype,
            'guest' => 0
        ));

        $session->set('person', (object) $person->getData());
    }

    /**
    *   Destroys the session for the given person
    *
    *   @param object ComPeopleDomainEntityPerson
    *   @return void
    */
    private function _destroySession(ComPeopleDomainEntityPerson $person)
    {
        $this->getService('repos:sessions.session')->destroy(array('nodeId' => $person->id));
        $this->getService('com:sessions')->destroy();
    }
}
