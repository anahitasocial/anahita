<?php

/**
 * Authenticate agains Google service.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectOauthServiceGoogle extends ComConnectOauthServiceAbstract
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'readonly' => true,
            'service_name' => 'Google',
            'api_url' => 'https://www.googleapis.com/oauth2/v1',
            //'authorize_url'     => 'https://accounts.google.com/o/oauth2/auth',
            //'access_token_url'  => 'https://accounts.google.com/o/oauth2/token'
            'request_token_url' => 'https://www.google.com/accounts/OAuthGetRequestToken',
            'authorize_url' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
            'access_token_url' => 'https://www.google.com/accounts/OAuthGetAccessToken',
        ));

        parent::_initialize($config);
    }

    /**
     * Get the access token using an authorized request token.
     *
     * @param array $data
     *
     * @return string
     */
    public function requestAdccessToken($data)
    {
        $post = array(
            'client_id' => $this->_consumer->key,
            'client_secret' => $this->_consumer->secret,
            'code' => $data->code,
            'redirect_uri' => $this->_consumer->callback_url,
            'grant_type' => 'authorization_code',
        );
        $response = $this->getRequest(array('url' => $this->access_token_url, 'method' => KHttpRequest::POST, 'data' => $post))->send();
        $result = $response->parseJSON();
        $this->setToken($result->access_token);

        return $result->access_token;
    }

    /**
     * {@inheritdoc}
     */
    public function canAddService($actor)
    {
        return $actor->inherits('ComPeopleDomainEntityPerson');
    }

     /**
      * Implements an empty post message. Google is a readyonly service.
      */
     public function postUpdate($message)
     {
         //Do nothing
     }

    /**
     * Return the current user data.
     *
     * @return array
     */
    protected function _getUserData()
    {
        $profile = $this->get('userinfo');
        $data = array(
            'id' => $profile->id ,
            'profile_url' => $profile->link,
            'name' => $profile->name,
            'large_avatar' => $profile->picture,
            'thumb_avatar' => $profile->picture,
        );

        return $data;
    }

    /**
     * Request for a request token.
     *
     * @param array $data
     *
     * @return string
     */
    public function requestRequestToken($data = array())
    {
        $data['scope'] = 'https://www.googleapis.com/auth/userinfo.profile';

        return parent::requestRequestToken($data);
    }

    /*
     * Return the authorize URL
     *
     * @param array $query Query to pass to the authorization URL
     * @return string
     */
     /*
    public function __getAuthorizationURL($query = array())
    {
        $query['scope']         = 'https://www.googleapis.com/auth/userinfo.profile';
        $query['redirect_uri']  = $this->_consumer->callback_url;
        $query['client_id']     = $this->_consumer->key;
        $query['response_type'] = 'code';
        $query['access_type']   = 'offline';
        return parent::getAuthorizationURL($query);
    }  */
}
