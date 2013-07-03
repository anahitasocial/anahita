<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Session Controller. Manages a session of a person
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleControllerSession extends ComBaseControllerResource
{
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->registerCallback('after.login', array($this, 'redirect'), 
                array('url'=>$config->redirect_to_after_login));
        
        $this->registerCallback('after.delete', array($this, 'redirect'),
                array('url'=>$config->redirect_to_after_logout));        
    }
    
    /**
    * Initializes the default configuration for the object
    *
    * you can set the redirect url for when a user is logged in
    * as follow
    * 
    * <code>
    * KService::setConfig('com://site/people.controller.session', array(
    *  'redirect_to_after_login'  => 'mynewurl'
    *  'redirect_to_after_logout' => 'mynewurl'        
    * )); 
    * </code>
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
            'redirect_to_after_login'  => '',
            'redirect_to_after_logout' => '',   
            //by default the format is json
            'request'   => array('format'=>'json')            
        ));

        parent::_initialize($config);
    }
            
    /**
     * Return the session
     * 
     * @param KCommandContext $context Command chain context 
     * 
     * @return void
     */
    protected function _actionRead(KCommandContext $context)
    {       
    	$person = $this->getService('repos://site/people.person')->find(array('userId'=>JFactory::getUser()->id));
    	$this->_state->setItem($person);
    	if ( isset($_SESSION['return']) ) {
    	    $this->_state->append(array('return'=>$_SESSION['return']));
    	}
    	return $person;
    }

    /**
     * Post method
     * 
     * @param KCommandContext $context
     * 
     * @return void
     */
    protected function _actionPost(KCommandContext $context)
    {
        try 
        {
            $result = $this->execute('add', $context);
            return $result;
        } 
        catch(RuntimeException $e) 
        {
            $context->response->setRedirect(JRoute::_('option=com_people&view=session'));
            throw $e;
        }
    }
    
    /**
     * Creates a new session
     * 
     * @param array   $user     The user as an array
     * @param boolean $remember Flag to whether remember the user or not
     * 
     * @return void
     * 
     * @throws LibBaseControllerExceptionUnauthorized If authentication failed
     * @throws LibBaseControllerExceptionForbidden    If person is authenticated but forbidden
     * @throws RuntimeException code for unkown error
     */
    public function login(array $user, $remember = false)
    {		
		$session  = &JFactory::getSession();
    		
		// we fork the session to prevent session fixation issues
		$session->fork();   
		JFactory::getApplication()->_createSession($session->getId());
    		
    	// Import the user plugin group
		JPluginHelper::importPlugin('user');
    	$options = array();	    	
    	$results = @JFactory::getApplication()->triggerEvent('onLoginUser', array($user, $options));
    	$failed  = false;
    		
		foreach($results as $result)
		{
			$failed = $result instanceof JException || $result instanceof Exception || $result === false;
			if ( $failed )
				break;
		}
    		
		if ( !$failed )
		{
			// Set the remember me cookie if enabled
			jimport('joomla.utilities.simplecrypt');
			jimport('joomla.utilities.utility');
    		
    			//if remeber is true or json api is being called
    			//return a cookie that contains the credential
			if ( $remember === true )
			{
    				//legacy for now
				$key      = JUtility::getHash(KRequest::get('server.HTTP_USER_AGENT','raw'));
				$crypt    = new JSimpleCrypt($key);
				$cookie   = $crypt->encrypt(serialize($user));
				$lifetime = time() + AnHelperDate::yearToSeconds();
				setcookie(JUtility::getHash('JLOGIN_REMEMBER'), $cookie, $lifetime, '/');
			}
			$context = $this->getCommandContext();
			$context->result = true;
			$this->getCommandChain()->run('after.login', $context);
			return true;
    		
		} else 
		{
			$user = $this->getService('repos://site/users.user')->fetch(array('username'=>$user['username']));
    		
			if ( $user && $user->block ) 
			{
			    $this->setMessage('COM-PEOPLE-AUTHENTICATION-PERSON-BLOCKED', 'error');		
			    throw new LibBaseControllerExceptionForbidden('User is blocked');
			}
						
			$this->setMessage('COM-PEOPLE-AUTHENTICATION-PERSON-UNKOWN', 'error');			
			throw new RuntimeException('Unkown Error');
		}

		// Trigger onLoginFailure Event		
		$this->setMessage('COM-PEOPLE-AUTHENTICATION-FAILED', 'error');
    	JFactory::getApplication()->triggerEvent('onLoginFailure', array((array)$user));
    	throw new LibBaseControllerExceptionUnauthorized('Authentication Failed. Check username/password');    	
    }
    
    /**
     * Authenticate a person and create a new session If a username password is passed then the user is first logged in. 
     * 
     * @param KCommandContext $context Command chain context 
     * 
     * @return void
     * 
     * @throws LibBaseControllerExceptionUnauthorized If authentication failed
     * @throws LibBaseControllerExceptionForbidden    If person is authenticated but forbidden
     * @throws RuntimeException for unkown error
     */
    protected function _actionAdd(KCommandContext $context)
    {
        $data     = $context->data;
        $url      = base64_decode($data->return);
        
        //if there's a sign up then 
        //change the redirect url
        if ( $data->return ) 
        {
            $_SESSION['return'] = $data->return;
            $url = base64UrlDecode($data->return);            
            $this->registerCallback('after.login', array($this, 'redirect'), array('url'=>$url));            
        }
        
        jimport('joomla.user.authentication');
           
        $authenticate = & JAuthentication::getInstance();
        $credentials  = KConfig::unbox($data);
        $options      = array();
        $authentication = $authenticate->authenticate($credentials, $options);
        if ( $authentication->status === JAUTHENTICATE_STATUS_SUCCESS )
        {
            $_SESSION['return'] = null;
            $this->login((array)$authentication, (bool)$data->remember);
            $this->getResponse()->status = KHttpResponse::CREATED;
        }
        else 
        {
            $this->setMessage('COM-PEOPLE-AUTHENTICATION-FAILED', 'error');
        	JFactory::getApplication()->triggerEvent('onLoginFailure', array((array)$authentication));
        	throw new LibBaseControllerExceptionUnauthorized('Authentication Failed. Check username/password');        	
        }
    }
    
    /**
     * Deletes a session and logs out the user
     * 
     * @param KCommandContext $context Command chain context 
     * 
     * @return void
     */
    protected function _actionDelete(KCommandContext $context)
    {
        //we don't care if a useris logged in or not just delete
        $context->response->status = KHttpResponse::NO_CONTENT;
        JFactory::getApplication()->logout();
    }

    /**
     * Redirect a user after login
     * 
     * @param KCommandContext $context
     * 
     * @return void
     */
    public function redirect(KCommandContext $context)
    {
        $url  = JRoute::_($context->url);
        $context->response->setRedirect($url);
    }
}