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
    public function __construct($dispatcher, $config = array())
    {
        // Command line fixes for Joomla
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

        if (
            !KService::get('application')->getSystemSetting('caching') ||
            (
                get_viewer()->superadmin() &&
                // @todo incorporate this feature in the global settings
                KRequest::get('get.clearapc', 'cmd')
            )
        ) {
            //clear apc cache for components
            //@NOTE If apc is shared across multiple services
            //this causes the caceh to be cleared for all of them
            //since all of them starts with the same prefix. Needs to be fix
            clean_apc_with_prefix('cache_com');
            clean_apc_with_prefix('cache_plg');
            clean_apc_with_prefix('cache_system');
            clean_apc_with_prefix('cache__system');
            $jconfig = new JConfig();
            clean_apc_with_prefix(md5($jconfig->secret).'-cache-');
        }

        KService::get('plg:storage.default');
        JFactory::getLanguage()->load('overwrite', JPATH_ROOT);
        JFactory::getLanguage()->load('lib_anahita', JPATH_ROOT);

        parent::__construct($dispatcher, $config);
    }

    /**
     * Remebers handling.
     */
    public function onAfterDispatch(KEvent $event)
    {
        $viewer = get_viewer();

        if (!$viewer->guest() && !$viewer->enabled) {
            KService::get('com:people.helper.person')->logout();
        }

        jimport('joomla.utilities.utility');
        jimport('joomla.utilities.simplecrypt');

        $credentials = array();
        $remember = JUtility::getHash('JLOGIN_REMEMBER');

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

        } elseif (
              $viewer->guest() &&
              isset($_COOKIE[$remember]) &&
              $_COOKIE[$remember] != ''
        ) {
            $key = JUtility::getHash(KRequest::get('server.HTTP_USER_AGENT', 'raw'));

            if ($key) {
                $crypt = new JSimpleCrypt($key);
                $cookie = $crypt->decrypt($_COOKIE[$remember]);
                $credentials = (array) @unserialize($cookie);
            }

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

    /**
     * store user method.
     *
     * Method is called after user data is stored in the database
     *
     * @param 	array		holds the new user data
     * @param 	bool		true if a new user is stored
     * @param	bool		true if user was succesfully stored in the database
     * @param	string		message
     */
    public function onAfterStoreUser(KEvent $event)
    {
        return true;
    }

    /**
     * delete user method.
     *
     * Method is called before user data is deleted from the database
     *
     * @param 	array		holds the user data
     */
    public function onBeforeDeleteUser(KEvent $event)
    {

        $person = KService::get('repos:people.person')->find(array(
                    'id' => $event->person->id
                  ));

        if ($person) {

            KService::get('repos:components')
            ->fetchSet()
            ->registerEventDispatcher(KService::get('anahita:event.dispatcher'));

            KService::get('anahita:event.dispatcher')
            ->dispatchEvent('onDeleteActor', array(
              'actor_id' => $person->id
            ));

            $person->delete()->save();
        }
    }
}
