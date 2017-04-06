<?php

/**
 * Anahita System Plugin.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class PlgSystemAnahita extends PlgAnahitaDefault
{
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
        if (in_array('suhosin', get_loaded_extensions())) {
            //Attempt setting the whitelist value
            @ini_set('suhosin.executor.include.whitelist', 'tmpl://, file://');

            //Checking if the whitelist is ok
            if (
                  !@ini_get('suhosin.executor.include.whitelist') ||
                  strpos(@ini_get('suhosin.executor.include.whitelist'), 'tmpl://') === false
            ) {
                $url = KService::get('application')->getRouter()->getBaseUrl();
                $url .= '/templates/system/error_suhosin.html';

                //@todo we don't have redirect methods
                KService::get('application.dispatcher')->getResponse()->setRedirect($url);
                KService::get('application.dispatcher')->getResponse()->send();

                return;
            }
        }

        //Safety Extender compatibility
        if (
            extension_loaded('safeex') &&
            strpos('tmpl', ini_get('safeex.url_include_proto_whitelist')) === false
        ) {
            $whitelist = ini_get('safeex.url_include_proto_whitelist');
            $whitelist = (strlen($whitelist) ? $whitelist.',' : '').'tmpl';
            ini_set('safeex.url_include_proto_whitelist', $whitelist);
        }

        KService::get('plg:storage.default');
        KService::get('anahita:language')->load('overwrite', ANPATH_ROOT);

        parent::__construct($dispatcher, $config);
    }

    /**
     * Remebers handling.
     */
    public function onAfterDispatch(KEvent $event)
    {
        $viewer = KService::get('com:people.viewer');

        if (!$viewer->guest() && !$viewer->enabled) {
            KService::get('com:people.helper.person')->logout();
            KService::get('application.dispatcher')->getResponse()->setRedirect(route('index.php'));
        }

        $credentials = array();
        $remember = get_hash('AN_LOGIN_REMEMBER');

        // for json requests obtain the username and password from the $_SERVER array
        // else if the remember me cookie exists, decrypt and obtain the username and password from it
        if (
               $viewer->guest() &&
               KRequest::has('server.PHP_AUTH_USER') &&
               KRequest::has('server.PHP_AUTH_PW') &&
               KRequest::format() == 'json'
           ) {

            $credentials['username'] = KRequest::get('server.PHP_AUTH_USER', 'raw');
            $credentials['password'] = KRequest::get('server.PHP_AUTH_PW', 'raw');

        } elseif ($viewer->guest() && isset($_COOKIE[$remember]) && $_COOKIE[$remember] != '') {

            $key = get_hash('AN_LOGIN_REMEMBER', 'md5');
            $crypt = $this->getService('anahita:encrypter', array('key' => $key, 'cipher' => 'AES-256-CBC'));
            $cookie = $crypt->decrypt($_COOKIE[$remember]);
            $credentials = (array) @unserialize($cookie);

        } else {
            return;
        }

        if ($viewer->guest() && count($credentials)) {

            try {

                $response = $this->getService('com:people.authentication.response');
                dispatch_plugin('authentication.onAuthenticate', array(
                                    'credentials' => $credentials,
                                    'response' => $response
                                ));

                if ($response->status === ComPeopleAuthentication::STATUS_SUCCESS) {
                    KService::get('com:people.helper.person')->login($credentials, true);
                }

            } catch (RuntimeException $e) {
                //only throws exception if we are using JSON format
                //otherwise let the current app handle it
                if (KRequest::format() == 'json') {
                    throw $e;
                }
            }
        }

        return;
    }
}
