<?php

/**
 * Authenticate agains linkedin service.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectOauthServiceLinkedin extends ComConnectOauthServiceAbstract
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
            'response_format' => 'xml',
            'service_name' => 'LinkedIn',
            'api_url' => 'https://api.linkedin.com/v1',
            'request_token_url' => 'https://api.linkedin.com/uas/oauth/requestToken?scope=w_share',
            'authorize_url' => 'https://www.linkedin.com/uas/oauth/authenticate',
            'access_token_url' => 'https://api.linkedin.com/uas/oauth/accessToken',
            'authenticate_url' => '',
        ));

        parent::_initialize($config);
    }

     /**
      * Post an status update to facebook for the logge-in user.
      *
      * @return array
      */
     public function postUpdate($message)
     {
         $data = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<share>
  <comment>$message</comment>
  <visibility>
     <code>anyone</code>
  </visibility>
</share>
EOT;

         $this->post('people/~/shares', $data);
     }

    /**
     * {@inheritdoc}
     */
    public function canAddService($actor)
    {
        return $actor->inherits('ComPeopleDomainEntityPerson');
    }

     /**
      * Return the current user data.
      *
      * @return array
      */
     protected function _getUserData()
     {
         $profile = (array) $this->get('people/~:(id,picture-url,first-name,last-name)');

         if (!isset($profile['id'])) {
             return;
         }

         $data = array(
            'id' => $profile['id'],
            'profile_url' => 'https://www.linkedin.com/profile/view?id='.$profile['id'],
            'name' => $profile['first-name'].' '.$profile['last-name'],
            //'username' => $profile->screen_name,
            'large_avatar' => $profile['picture-url'],
            'thumb_avatar' => $profile['picture-url'],
        );

         return $data;
     }
}
