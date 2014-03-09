<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Provides oatuh authentication workflow to both login/setting controller
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectControllerBehaviorOauthorizable extends KControllerBehaviorAbstract
{
	/**
	 * OAuth Adapter API
	 * 
	 * @var ComConnectOauthApiAbstract
	 */
	protected $_api;	
	
	/**
	 * Consumer 
	 * 
	 * @var ComConnectOauthConsumer
	 */
	protected $_consumer;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
					
		$this->_consumer = $config->consumer;
		
		if ( $config->api ) {
			$this->_api  = $config->api;
			$this->_api->setConsumer( $this->_consumer );
		}
		
		$this->registerActionAlias('post','oauthorize');
	}
		
	/**
 	 * Handles callback 
 	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _actionGetaccesstoken($context)
	{			    
		$this->getAPI()->requestAccessToken($this->getRequest());
		$token		= (array) $this->getAPI()->getToken();
		$consumer	= (array)$this->_consumer;
		KRequest::set('session.oauth', array('api'=>$this->getAPI()->getName(),'token'=>$token, 'consumer'=>$consumer));
		$return = KRequest::get('session.return','raw', null);
		if ( $return )
		    $context->append(array('data'=>array('return'=>$return)));
	}
		
	/**
 	 * Authorize an oauth profile to an actor. It needs to authorize 
 	 * 
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _actionOauthorize($context)
	{	
	    $data = $context->data;
	    KRequest::set('session.return', (string)$data->return);
		KRequest::set('session.oauth', null);
		$view = $this->_mixer->getIdentifier()->name;		
		$this->getAPI()->setToken(null);		
		$context->response->setRedirect($this->getAPI()->getAuthorizationURL());
	}

	/**
	 * Set the api to the data in the context
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _beforeControllerGet(KCommandContext $context)
	{
		if ( $this->_mixer->getRequest()->get == 'accesstoken' ) 
        {
			$this->_mixer->execute('getaccesstoken', $context);
			return false;
		}
		else 
            $this->api = $this->getAPI();
	}
	
	/**
	 * Get the oatuh adapter using the controller name
	 * 
	 * @return ComConnectOauthApiAbstract
	 */
	public function getAPI()
	{
		if ( !isset($this->_api) ) 
		{
			$session = new KConfig(KRequest::get('session.oauth', 'raw', array()));
			
			$api = pick($this->getRequest()->server, $session->api);
			
			if ( !$api )
				return null;
				
			//get the api server from the request
			$this->_api = $this->getService('com://site/connect.oauth.service.'.$api);
			
			if ( !$this->_consumer ) 
			{
				$name = $this->_api->getName();
				$key  	= get_config_value('com_connect.'.$name.'_key');
				$secret = get_config_value('com_connect.'.$name.'_secret');
				
				$query = array(
				     'option=com_'.$this->_mixer->getIdentifier()->package,
				     'view='.$this->_mixer->getIdentifier()->name,				    
				     'server='.$this->server,
				     'get=accesstoken'
				);
				if ( $this->oid ) {
				    $query[] = 'oid='.$this->oid;
				}				
				$callback = JRoute::_(implode($query,'&'), true);
				$this->_consumer = new ComConnectOauthConsumer(new KConfig(array(
					'key'	      => $key, 
					'secret'      => $secret, 
					'callback_url'=> (string)$callback
				)));
			}
			
			//set the consumer
			$this->_api->setConsumer( $this->_consumer );
			
			$this->_api->setToken($session->token);
		}
				
		return $this->_api;
	}	
    
    /**
     * Check if oauthorize is allowed
     * 
     * @return boolean
     */
    public function canOauthorize()
    {
        $api = $this->getAPI();
        
        if ( $api && $this->actor ) {
           return $api->canAddService($this->actor);             
        }
        
        return !is_null($api);
    }
}