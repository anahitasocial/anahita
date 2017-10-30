<?php

/**
 * Anahita Tweets User Plugin.
 *
 * @author		Rastin Mehr  <info@rmdstudio.com>
 *
 * @category     Anahita
 */
class PlgUserConnect extends PlgAnahitaDefault
{
    /**
     * API.
     *
     * @var ComConnectOauthApiAbstract
     */
    protected $_api;

    /**
     * Person.
     *
     * @var ComPeopleDomainEntityPerson
     */
    protected $_person;

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
    public function onAfterAddPerson(KEvent $event)
    {
        $person = $event->person;

        if (! $this->_canPerform()) {
            return false;
        }

        $this->_createToken($person->username);

        $user = $this->_api->getUser();

        if (KRequest::get('post.import_avatar', 'cmd') && $user->large_avatar) {
            $person->setPortraitImage(array('url' => $user->large_avatar));
        }

        $person->enabled = true;
        $person->save();
    }

    /**
     * This method should handle any login logic and report back to the subject.
     *
     * @param 	array 	holds the user data
     * @param 	array    extra options
     *
     * @return bool True on success
     */
    public function onBeforeLoginPerson(KEvent $event)
    {
        $credentials = $event->credentials;

        if (! $this->_canPerform()) {
            return false;
        }

        if (isset($credentials['username'])) {
            $this->_createToken($credentials['username']);
        }
    }

    /**
     * Creates a connect token.
     */
    protected function _createToken($username)
    {
        if (isset($this->_person)) {
            return;
        }

        $api = $this->_getApi();

        //if there's no api or the token are invalid then don't create
        //session
        if (!$api || !$api->getUser()->id) {
            return;
        }

        if ($token = $api->getToken()) {

            $person = KService::get('repos:people.person')->find(array('username' => $username));
            $user = $api->getUser();
            $session = KService::get('repos:connect.session')->findOrAddNew(array('profileId' => $user->id, 'api' => $api->getName()));

            $session->setData(array(
                'component' => 'com_connect',
                'owner' => $person,
            ))->setToken($token);

            $session->save();

            $this->_person = $person;
            $this->_api = $api;
        }
    }

    /**
     * Creates an api object either from the session or the values in the post.
     */
    protected function _getApi()
    {
        $post = KRequest::get('post', 'string');

        $api = null;

        try {

            if (isset($post['oauth_token']) && isset($post['oauth_handler'])) {
                $api = ComConnectHelperApi::getApi($post['oauth_handler']);
                $api->setToken($post['oauth_token'], isset($post['oauth_secret']) ? $post['oauth_secret'] : '');
            } else {
                $session = new KConfig(KRequest::get('session.oauth', 'raw', array()));

                if (!$session->token || !$session->api || !$session->consumer) {
                    return;
                }

                KRequest::set('session.oauth', null);

                KService::get('koowa:loader')->loadIdentifier('com://site/connect.oauth.consumer');

                $api = KService::get('com:connect.oauth.service.'.$session->api, array(
                    'consumer' => new ComConnectOauthConsumer($session->consumer),
                    'token' => $session->token,
                ));
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            $api = null;
        }

        return $api;
    }

    private function _canPerform()
    {
        $option = KRequest::get('get.option', 'cmd', '');
        return $option == 'com_connect' ? true : false;
    }
}
