<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth_Service
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Authenticate agains Twitter service
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth_Service
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectOauthServiceTwitter extends ComConnectOauthServiceAbstract
{		
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'service_name'		=> 'Twitter',
			'api_url'            => 'https://api.twitter.com/1.1',
			'request_token_url' => 'https://api.twitter.com/oauth/request_token' ,
			'authorize_url'		=> 'https://api.twitter.com/oauth/authenticate',
			'access_token_url'  => 'https://api.twitter.com/oauth/access_token' ,
			'authenticate_url'  => '' 		
		));
	
		parent::_initialize($config);
	}
	
    /**
    * @inheritDoc
    */
    public function canAddService($actor)
    {
        return true;
    }
        
	/**
	 * Post an status update to facebook for the logge-in user  
	 * 
	 * @return array
	 */
	 public function postUpdate($message)
	 {
	 	$this->post('statuses/update.json', array('status'=>$message));	 	
	 }
	 
	 /**
	  * Return the current user data
	  * 
	  * @return array
	  */
	 protected function _getUserData()
	 {
	 	$profile = $this->get('account/verify_credentials.json');
            
        $data = array(
            'id'            => $profile->id ,
            'profile_url'   => 'http://twitter.com/'.$profile->username,
            'name'     => $profile->name,
            'username' => $profile->screen_name,
            'large_avatar'  => 'http://api.twitter.com/1/users/profile_image?screen_name='.$profile->screen_name.'&size=original',
            'thumb_avatar'  => $profile->profile_image_url          
        );
        
        return $data;
	 }
}
?>
