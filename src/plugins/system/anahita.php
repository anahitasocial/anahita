<?php

/**
 * Anahita System Plugin.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class PlgSystemAnahita extends PlgAnahitaDefault
{
    /**
    *   Represents the person who has logged in
    *   @param ComPeopleViewer
    */
    private $_viewer = null;

    /**
     * Constructor.
     *
     * @param mixed $subject Dispatcher
     * @param array $config  Array of configuration
     */

    public function __construct($dispatcher, KConfig $config)
    {
        // Command line fixes for Anahita
        if (PHP_SAPI === 'cli') {
            if (!isset($_SERVER['HTTP_HOST'])) {
                $_SERVER['HTTP_HOST'] = '';
            }

            if (!isset($_SERVER['REQUEST_METHOD'])) {
                $_SERVER['REQUEST_METHOD'] = '';
            }
        }

        // Check for suhosin
        $this->_handleSuhosinCheck();

        //Safety Extender compatibility
        $this->_handleSafeexCheck();

        //load language overwrites
        KService::get('anahita:language')->load('overwrite', ANPATH_ROOT);

        //create viewer object
        $this->_viewer = KService::get('com:people.viewer');

        parent::__construct($dispatcher, $config);
    }

    /**
     * Remebers handling.
     */
    public function onAfterDispatch(KEvent $event)
    {
        $this->_logoutIfDisabledAccount();

        if (! $this->_viewer->guest()) {
            return;
        }

        if (KRequest::method() === 'GET') {
            $this->_handleAutoLogin();
        }

        return;
    }

    /**
    *   Handles all scenarios where user is logged back in.
    *   For example if they had checked "Stay Logged In" last time that they'd logged on.
    *
    *   @return void
    */
    private function _handleAutoLogin()
    {
        $credentials = $this->_getRememberMeCredentials();

        if (count($credentials)) {
            if ($this->_authenticate($credentials)) {
                $this->_login($credentials);
            }
        }
    }

    /**
    *   Authenticate the credentials
    *
    *   @param array array('username' => 'janesmith', 'password' => 'somethingsecure')
    *   @return boolean TRUE if authentication passes
    */
    private function _authenticate($credentials)
    {
        try {
            $response = $this->getService('com:people.authentication.response');
            dispatch_plugin('authentication.onAuthenticate', array(
                                'credentials' => $credentials,
                                'response' => $response
                            ));

            if ($response->status === ComPeopleAuthentication::STATUS_SUCCESS) {
                return true;
            }

        } catch (RuntimeException $e) {
            //only throws exception if we are using JSON format
            //otherwise let the current app handle it
            if (KRequest::format() == 'json') {
                throw $e;
            }
        }

        return false;
    }

    /**
    *   Loggin the user and keep them logged in
    *
    *   @param array array('username' => 'janesmith', 'password' => 'somethingsecure')
    *   @return void
    */
    private function _login($credentials) {
        $this->getService('com:people.helper.person')->login($credentials);
        $this->getService('application.dispatcher')
        ->getResponse()
        ->setRedirect($this->_getCurrentUrl())
        ->send();
    }

    /**
    *   Obtains credentials for the people who have obted to stay logged in
    *
    *   @return array array('username' => 'janesmith', 'password' => 'somethingsecure')
    */
    private function _getRememberMeCredentials()
    {
        $credentials = array();
        $remember = get_hash('AN_LOGIN_REMEMBER');

        if (isset($_COOKIE[$remember]) && !empty($_COOKIE[$remember])) {
            $key = get_hash('AN_LOGIN_REMEMBER', 'md5');
            $crypt = $this->getService('anahita:encrypter', array('key' => $key, 'cipher' => 'AES-256-CBC'));
            $cookie = $crypt->decrypt($_COOKIE[$remember]);

            try {
                $credentials = (array) unserialize($cookie);
            } catch (RuntimeException $e) {
                error_log($e->getMessage());
            }
        }

        return $credentials;
    }

    /**
    *   Logout a user with disabled account
    *
    *   @return void
    */
    private function _logoutIfDisabledAccount()
    {
        if (! is_null($this->_viewer)) {
            if (! $this->_viewer->guest() && ! $this->_viewer->enabled) {
                $this->getService('com:people.helper.person')->logout();
                $this->getService('application.dispatcher')->getResponse()->setRedirect(route('index.php'));
            }
        }

        return;
    }

    /**
    *   Returns existing url location
    *
    *   @return string
    */
    private function _getCurrentUrl()
    {
        $url = $this->getService('com:application')->getRouter()->getBaseUrl();
        $url .= $_SERVER['REQUEST_URI'];

        return $url;
    }

    private function _handleSuhosinCheck()
    {
        if (in_array('suhosin', get_loaded_extensions())) {
            //Attempt setting the whitelist value
            @ini_set('suhosin.executor.include.whitelist', 'tmpl://, file://');

            //Checking if the whitelist is ok
            if (
                  !@ini_get('suhosin.executor.include.whitelist') ||
                  strpos(@ini_get('suhosin.executor.include.whitelist'), 'tmpl://') === false
            ) {
                $url = $this->getService('com:application')->getRouter()->getBaseUrl();
                $url .= '/templates/system/error_suhosin.html';

                KService::get('application.dispatcher')
                ->getResponse()
                ->setRedirect($url)
                ->send();
            }
        }

        return;
    }

    private function _handleSafeexCheck()
    {
        if (
            extension_loaded('safeex') &&
            strpos('tmpl', ini_get('safeex.url_include_proto_whitelist')) === false
        ) {
            $whitelist = ini_get('safeex.url_include_proto_whitelist');
            $whitelist = (strlen($whitelist) ? $whitelist.',' : '').'tmpl';
            ini_set('safeex.url_include_proto_whitelist', $whitelist);
        }
    }
}
