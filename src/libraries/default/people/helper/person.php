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
class LibPeopleHelperPerson extends KObject
{
    /**
     * Logs in a user.
     *
     * @param array $user     The user as an array
     * @param bool  $remember Flag to whether remember the user or not
     *
     * @return bool
     */
    public function login(array $user, $remember = false)
    {

        $session = KService::get('anahita:session');

        // we fork the session to prevent session fixation issues
        $session->fork();

        $application = KService::get('application');
        $application->_createSession($session->getId());

        $options = array();
        $results = dispatch_plugin('user.onLoginUser', array(
                      'user' => $user,
                      'options' => $options
                    ));

        foreach ($results as $result) {
            if (
                $result instanceof JException ||
                $result instanceof Exception ||
                $result === false
                ) {
                return false;
            }
        }

        //if remember is true, create a remember cookie that contains the ecrypted username and password
        if ($remember) {
            // Set the remember me cookie if enabled
            jimport('joomla.utilities.simplecrypt');
            jimport('joomla.utilities.utility');

            $key = JUtility::getHash(KRequest::get('server.HTTP_USER_AGENT', 'raw'));

            if ($key) {
                $crypt = new JSimpleCrypt($key);
                $cookie = $crypt->encrypt(serialize(array(
                    'username' => $user['username'],
                    'password' => $user['password'],
                )));
                $lifetime = time() + (365 * 24 * 3600);
                setcookie(JUtility::getHash('JLOGIN_REMEMBER'), $cookie, $lifetime, '/');
            }
        }

        return true;
    }

    /**
     * Deletes a session and logs out the viewer.

     * @return bool
     */
    public function logout()
    {
        $viewer = get_viewer();
        $user = array(
          'username' => $viewer->username,
          'id' => $viewer->userId
        );

        $results = dispatch_plugin('user.onLogoutUser', array( 'user' => $user ));

        if ($results) {
    			setcookie(JUtility::getHash('JLOGIN_REMEMBER'), false, time() - AnHelperDate::dayToSeconds(30), '/');
    			return true;
    		}

        return false;
    }
}
