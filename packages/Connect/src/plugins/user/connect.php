<?php 
/**
 * @version		1.0.3
 * @category	Anahita Social Engine™
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

/**
 * Anahita Tweets User Plugin
 *
 * @author		Rastin Mehr  <info@rmdstudio.com>
 * @package		Joomla
 * @subpackage	Anahita
 * @since 		1.5
 */
class PlgUserConnect extends JPlugin 
{
	/**
	 * API
	 *
	 * @var ComConnectOauthApiAbstract
	 */
	protected $_api;
	
	/**
	 * Person
	 *
	 * @var ComPeopleDomainEntityPerson       
	 */
	protected $_person;	
		
	/**
	 * store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	public function onAfterStoreUser($user, $isnew, $succes, $msg)
	{		
		$this->_createToken($user['username']);
		$userId = $user['id'];
		if ( $this->_person ) 
        {
		    //unblock the user	
            $user = KService::get('repos://site/users')->find($userId);
            $user->block = false;
            $user->save();            
			$user = $this->_api->getUser();
			if ( KRequest::get('post.import_avatar', 'cmd') && $user->large_avatar ) {
				$this->_person->setPortraitImage(array('url'=>$user->large_avatar));
			}
			$this->_person->enabled = true;
			$this->_person->save();
		}
	}
		
	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @access	public
	 * @param 	array 	holds the user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 * @since	1.5
	 */	
	public function onLoginUser($user, $options)
	{				
		if ( isset($user['username']) ) {
			$this->_createToken($user['username']);
		}
	}
	
	/**
	 * Creates a connect token 
	 * 
	 * @return void
	 */
	protected function _createToken($username)
	{
        if ( isset($this->_person) ) {            
            return ;
        }
        
        $api = $this->_getApi();
        
        //if there's no api or the token are invalid then don't create 
        //session
        if ( !$api || !$api->getUser()->id )
            return;
				
        if ( $token = $api->getToken() )
        {	
            $person   = KService::get('repos://site/people.person')->find(array('username'=>$username));
            $user     = $api->getUser();
            $session  = KService::get('repos://site/connect.session')->findOrAddNew(array('profileId'=>$user->id,'api'=>$api->getName()));
            $session->setData(array(
                'component' => 'com_connect',
                'owner'     => $person  
            ))->setToken($token);            
            $session->save();            
            $this->_person = $person;
            $this->_api    = $api;
        }
	}
    
    /**
     * Creates an api object either from the session or the values in the post
     */
    protected function _getApi()
    {
        $post = KRequest::get('post','string');
        $api  = null;
        try {
            if ( isset($post['oauth_token']) &&
                 isset($post['oauth_handler']))
            {
                    $api = ComConnectHelperApi::getApi($post['oauth_handler']);
                    $api->setToken($post['oauth_token'], isset($post['oauth_secret']) ? $post['oauth_secret'] : '');
            } 
            
            else 
            {
                $session = new KConfig(KRequest::get('session.oauth', 'raw', array()));
                                    
                if ( !$session->token || !$session->api || !$session->consumer )
                    return;
                
                KRequest::set('session.oauth', null);
            
                KService::get('koowa:loader')->loadIdentifier('com://site/connect.oauth.consumer');
                                    
                $api = KService::get('com://site/connect.oauth.service.'.$session->api, array(
                    'consumer'  => new ComConnectOauthConsumer($session->consumer),
                    'token'     => $session->token
                ));            
            }
        } catch(Exception $e) {
          $api = null;  
        }
        
        return $api;
    }
}