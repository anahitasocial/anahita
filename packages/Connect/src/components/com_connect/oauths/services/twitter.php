<?php

/**
 * Authenticate agains Twitter service.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectOauthServiceTwitter extends ComConnectOauthServiceAbstract
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
            'service_name' => 'Twitter',
            'version' => '1.0',
            'api_url' => 'https://api.twitter.com/1.1',
            'request_token_url' => 'https://api.twitter.com/oauth/request_token',
            'authorize_url' => 'https://api.twitter.com/oauth/authorize',
            'access_token_url' => 'https://api.twitter.com/oauth/access_token',
            'authenticate_url' => 'https://api.twitter.com/oauth/authenticate',
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
      * Post an status update to facebook for the logge-in user.
      *
      * @return array
      */
     public function postUpdate($message)
     {
         $this->post('statuses/update.json', array('status' => $message));
     }

     /**
      * Return the current user data.
      *
      * @return array
      */
     protected function _getUserData()
     {
         $profile = $this->get('account/verify_credentials.json'); 
         $data = array(
            'id' => $profile->id ,
            'profile_url' => 'https://twitter.com/'.$profile->screen_name,
            'name' => $profile->name,
            'username' => $profile->screen_name,
            'thumb_avatar' => $profile->profile_image_url_https,
        );
        
        return $data;
     }
}
