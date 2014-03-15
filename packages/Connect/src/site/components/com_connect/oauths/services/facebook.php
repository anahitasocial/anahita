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
 * Authenticate agains Facebook service
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth_Service
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectOauthServiceFacebook extends ComConnectOauthServiceAbstract
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
			'service_name'		=> 'Facebook',
			'version'			=> '2.0',
			'api_url'			=> 'https://graph.facebook.com' ,
			'request_token_url' => '' ,
			'access_token_url'  => 'https://graph.facebook.com/oauth/access_token' ,
			'authenticate_url'  => '' ,
			'authorize_url'		=> 'https://graph.facebook.com/oauth/authorize'
		));
	
		parent::_initialize($config);
	}	
	
    /**
    * @inheritDoc
    */
    public function canAddService($actor)
    {
        return $actor->inherits('ComPeopleDomainEntityPerson');
    }
        	
	/**
	 * Get the access token using an authorized request token
	 * 
	 * @param array $data 
	 * @return string
	 */
	public function requestAccessToken($data)
	{
		$url 	   = $this->access_token_url . "?type=user_agent&client_id=" . $this->_consumer->key . "&client_secret=". $this->_consumer->secret . "&code=" . $data->code;
		$url	  .= '&redirect_uri='.$this->_consumer->callback_url;
		$response  = $this->getRequest(array('url'=>$url))->send(); 
		$result	   = $response->parseQuery();
		$this->setToken($result->access_token);		
		return $result->access_token;
	}		
	
	/**
	 * Post an status update to facebook for the logge-in user  
	 * 
	 * @return array
	 */
	 public function postUpdate($message)
	 {
	 	$this->post('me/feed', array('message'=>$message));
	 }
	 
     /**
      * Return the current user data
      * 
      * @return array
      */
     protected function _getUserData()
     {	 	
     	   $me = $this->get('me');            
           $data = array(
                'profile_url'  => 'http://www.facebook.com/profile.php?id='.$me->id,
                'name'         =>  $me->name,
                'id'           => $me->id,
                'username'     => $me->username,
                'email'        => $me->email,
                'description'  => $me->about,
                'thumb_avatar' => $this->getRequest(array('url'=>$this->api_url.'/me/picture'))->getURL(),
                'large_avatar' => $this->getRequest(array('url'=>$this->api_url.'/me/picture','data'=>array('type'=>'large')))->getURL()
            );
            return $data;
	}
	 		
	/**
	 * Return the authorize URL
	 * 
	 * @param array $query Query to pass to the authorization URL
	 * @return string
	 */
	public function getAuthorizationURL($query = array())
	{
		$query['scope']		   = implode(',', array('offline_access','publish_stream', 'user_about_me','email'));
		$query['redirect_uri'] = $this->_consumer->callback_url;		
		$query['client_id']	   = $this->_consumer->key;
		
		return parent::getAuthorizationURL($query);		
	}
	
	/**
     * Return a set of people who are fb friends
     * 
     * @return set
     */
    public function getFriends()
    {
        $cache = JFactory::getCache((string) 'ComConnectOauthServiceFacebook', '');
        $key   = 'ids_'.md5($this->_token);
        $data  = $cache->get($key);        
        if ( !$data  )
        {
            try {
                $data   = $this->get('/me/friends');
            } catch(Exception $e) {
                throw new \LogicException("Can't get connections from facebook");
            }
            if ( $data->error ) {
                throw new \LogicException("Can't get connections from facebook");
            }            
            $data   = KConfig::unbox($data);
            $data   = array_map(function($user) {return $user['id'];}, $data['data']);            
            $data[] = '-1'; 
            $cache->store(json_encode($data), $key);
        } else {
            $data = json_decode($data);
        }
        
        $query = $this->getService('repos://site/people')->getQuery(true)
        ->where(array(
                'sessions.profileId'=> $data,
                'sessions.api'      => 'facebook'));
        return $query->toEntitySet();       
    } 
    
     /**
     * Return the APPID
     * 
     * @return int
     */ 
    public function getAppID()
    {        
    	$key   = md5($this->_token);
        $cache = JFactory::getCache((string) 'ComConnectOauthServiceFacebook');
        $cache->setLifeTime(5*1000);
        
        
        $data = $cache->get(function($session) {
            $info = $session->get('/app');
            return $info;
        }, array($this) , '/app'.$key);
        return $data['id'];        
    }
}