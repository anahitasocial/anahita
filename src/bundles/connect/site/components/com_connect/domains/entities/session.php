<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */


/**
 * Session object. After an actor has been authenticated, session store its authentication
 * token/value
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectDomainEntitySession extends AnDomainEntityDefault
{	
	/**
	 * Initializes the default configuration for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'resources'		=> array('table'=>'connect_sessions'),
			'attributes'	=> array(
                'id'=>array('key'=>true),				
				'component',
				'tokenKey',
				'tokenSecret',
				'api'			=> array('write_access'=>'protected'),
				'profileId'
			),
			'relationships' => array(
				'owner' => array(
					'polymorphic'  => true, 
					'required'	   => true, 
					'parent'	   => 'com:actors.domain.entity.actor', 
					'inverse'	   => true			
				)
			)
		));	
		
		parent::_initialize($config);		
	}
	
	/**
	 * Return an instance of API Adapter
	 * 
	 * @return ComConnectOauthApiAbstract
	 */
	public function getApi()
	{
		if ( !isset($this->_api) ) {
			$this->_api = ComConnectHelperApi::getApi($this->get('api'));
			$this->_api->setToken($this->tokenKey, $this->tokenSecret);
		}
		
		return $this->_api;
	}
	
	/**
	 * Return the Session Name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->get('api');
	}
	
	/**
	 * Sets the oauth token attributes
	 *
	 * @param 	OAuthToken $token
	 * @return	ComConnectDomainEntityToken
	 */
	public function setToken($token)
	{
		$this->tokenKey    = $token->key;
		$this->tokenSecret = $token->secret;
		return $this;
	}

	/**
	 * Echo a story
	 * 
	 * @param  array $data
	 * @return void
	 */
	public function echoStory($data)
	{
		$service = $this->getService('com:connect.domain.echoer.'.$this->get('api'));
		return $service->echoStory($this, $data);
	}
	
	/**
	 * Gets a resource
	 *
	 * @param  string $resource
	 * @return mixed
	 */
	public function getResource($path)
	{
		return $this->getApi()->get($path);
	}
	
	/**
	 * Post data to a resource
	 *
	 * @param  string $resource
	 * @param  array  $data
	 * @return mixed
	 */
	public function postData($path, $data = array())
	{
		return $this->getApi()->post($path, $data);
	}
    
    /**
     * Validate the token
     * 
     * @return boolean
     */
    public function validateToken()
    {        
        return $this->api->getUser()->id;
    }
}