<?php

/**
 * Authenticate agains Facebook service.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectOauthServiceFacebook extends ComConnectOauthServiceAbstract
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'service_name' => 'Facebook',
            'readonly' => true,
            'version' => '2.8',
            'api_url' => 'https://graph.facebook.com',
            'request_token_url' => '',
            'access_token_url' => 'https://graph.facebook.com/oauth/access_token',
            'authenticate_url' => 'https://graph.facebook.com/oauth/authenticate',
            'authorize_url' => 'https://graph.facebook.com/oauth/authorize',
        ));

        parent::_initialize($config);
    }

    /**
     * {@inheritdoc}
     */
    public function canAddService($actor)
    {
        return $actor->inherits('ComPeopleDomainEntityPerson');
    }

    /**
     * Get the access token using an authorized request token.
     *
     * @param array $data
     *
     * @return string
     */
    public function requestAccessToken($data)
    {
        $params = array(
            'client_id' => $this->_consumer->key,
            'client_secret' => $this->_consumer->secret,
            'code' => $data->code,
            'redirect_uri' => $this->_consumer->callback_url,
            'response_type' => 'token'
        );

        $url = $this->access_token_url.'?'.http_build_query($params);
        $response = $this->getRequest(array('url' => $url))->send();
        $result = $response->parseJSON();
        $this->setToken($result->access_token);

        return $result->access_token;
    }

     /**
      * Post an status update to facebook for the logge-in user.
      *
      * @return array
      */
     public function postUpdate($message)
     {
         $this->post('me/feed', array('message' => $message));
     }

     /**
      * Return the current user data.
      *
      * @return array
      */
     protected function _getUserData()
     {
         $me = $this->get('me');
         
         $data = array(
                'profile_url' => 'https://www.facebook.com/profile.php?id='.$me->id,
                'name' => $me->name,
                'id' => $me->id,
                'username' => $me->username,
                'email' => $me->email,
                'description' => $me->about,
                'thumb_avatar' => $this->getRequest(array('url' => $this->api_url.'/me/picture'))->getURL(),
                'large_avatar' => $this->getRequest(array(
                    'url' => $this->api_url.'/me/picture', 
                    'data' => array('type' => 'large')
                ))->getURL(),
            );

         return $data;
     }

    /**
     * Return the authorize URL.
     *
     * @param array $query Query to pass to the authorization URL
     *
     * @return string
     */
    public function getAuthorizationURL($query = array())
    {
        $permissions = array(
            'email', 
            'user_friends',
        );
        $query['scope'] = implode(',', $permissions);
        $query['redirect_uri'] = $this->_consumer->callback_url;
        $query['client_id'] = $this->_consumer->key;

        return parent::getAuthorizationURL($query);
    }

    /**
     * Return a set of people who are fb friends.
     *
     * @return set
     */
    public function getFriends()
    {
        try {
            $data = $this->get('/me/friends');
        } catch (Exception $e) {
            throw new \LogicException("Can't get connections from facebook");
        }

        if ($data->error) {
            throw new \LogicException("Can't get connections from facebook");
        }

        $data = KConfig::unbox($data);
        $data = array_map(function ($user) {return $user['id'];}, $data['data']);
        $data[] = '-1';

        $query = $this->getService('repos:people.person')
        ->getQuery(true)
        ->where(array(
            'sessions.profileId' => $data,
            'sessions.api' => 'facebook'
        ));

        return $query->toEntitySet();
    }

    /**
     * Return the APPID.
     *
     * @return int
     */
    public function getAppID()
    {
        $data = $this->get('/app');
        $data = KConfig::unbox($data);
        return $data['id'];
    }
}
