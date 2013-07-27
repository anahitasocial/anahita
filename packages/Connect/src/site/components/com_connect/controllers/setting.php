<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Connect Setting Contorller
 * 
 * This is not a dispatchable controller, but it's called as HMVC from an actor 
 * setting page
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectControllerSetting extends ComBaseControllerResource
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
			'behaviors' => array(
				'oauthorizable',
				'ownable'			
			)
		));
	
		parent::_initialize($config);
	}
	
	/**
	 * Removes a token 
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @param void
	 */
	protected function _actionDelete(KCommandContext $context)
	{	
	    $this->getResponse()->status = KHttpResponse::NO_CONTENT;
	    
		$token	= $this->getService('repos://site/connect.session')
		    ->fetchSet(array('owner'=>$this->actor,'api'=>$this->getAPI()->getName()));		
		$token->delete()->save();
	}
	
	/**
	 * After getting the access token store the token in the session and redirect
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @param void
	 */
	protected function _actionGetaccesstoken(KCommandContext $context)
	{
	    $data = $context->data;
		$this->getBehavior('oauthorizable')->execute('action.getaccesstoken', $context);				
		$user	 = $this->getAPI()->getUser();
		$session = $this->getService('repos://site/connect.session')->findOrAddNew(array('profileId'=>$user->id,'api'=>$this->getAPI()->getName()));
        $token   = $this->getAPI()->getToken();
        if (!empty($token) ) 
        {
            $session->setData(array(
                'component' => 'com_connect',
                'owner'     => $this->actor
            ))->setToken($token)->save();
        }
        $route = JRoute::_($this->actor->getURL().'&get=settings&edit=connect');
        if ( $data->return ) {
            $route = base64_decode($data->return);
        }
		$context->response->setRedirect($route);
	}
	
	/**
	 * Get action
	 * 
	 * Renders the actor setting for connect
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @param void
	 */
	protected function _actionRead(KCommandContext $context)
	{		
		$apis = ComConnectHelperApi::getServices();
				
		$this->getService('repos:connect.session');
		
		$sessions = $this->actor->sessions;

		foreach($apis as $key => $api) 
        {           
            if ( !$api->canAddService($this->actor) )
			     unset($apis[$key] );                
		}

        $this->apis     = $apis;
        $this->sessions = $sessions;
	}
}

?>
