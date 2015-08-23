<?php

/**
 * Session Controller. Manages a session of a person
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
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
  
        $this->registerCallback(
            'after.add', 
            array($this, 'redirect'), 
            array('url'=>$config->redirect_to_after_login)
            );
        
        $this->registerCallback(
            'after.delete', 
            array($this, 'redirect'),
            array('url'=>$config->redirect_to_after_logout)
            );    
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
            'redirect_to_after_login' => '',
            'redirect_to_after_logout' => '',   
            //by default the format is json
            'request' => array(
            	'format'=> 'json'
        	)          
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
    	$person = $this->getService('repos://site/people.person')
    	               ->find(array(
    	                   'userId' => JFactory::getUser()->id
                           ));
    	
    	$this->_state->setItem($person);
    	
    	if (isset($_SESSION['return'])) {
    	    $this->_state->append(array(
    	        'return'=>$this->getService('com://site/people.filter.return')
    	                       ->sanitize($_SESSION['return']))
                );
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
        try {        
            $result = $this->execute('add', $context);
            return $result;    
        } catch(RuntimeException $e) {        
            $context->response->setRedirect(JRoute::_('option=com_people&view=session'));
            throw $e;
        }
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
        $data = $context->data;
        
        $user = array(
            'username' => $data->username,
            'password' => $data->password,
            'remember' => $data->remember
        );
        
        if (!$this->getService('com:people.helper.person')->login($user, $user['remember'])) {        
            $user = $this->getService('repos://site/users.user')
                         ->fetch(array('username'=>$user['remember']));
            
            if (! $user) {
                $this->setMessage('COM-PEOPLE-AUTHENTICATION-PERSON-UNKOWN', 'error');     
                throw new RuntimeException('Unkown Error');
            } elseif ($user && $user->block) {
                $this->setMessage('COM-PEOPLE-AUTHENTICATION-PERSON-BLOCKED', 'error'); 
                throw new LibBaseControllerExceptionUnauthorized('User is blocked');
            } else {
                // Trigger onLoginFailure Event
                JFactory::getApplication()->triggerEvent('onLoginFailure', array((array) $user));            
                $this->setMessage('COM-PEOPLE-AUTHENTICATION-FAILED', 'error');
                throw new LibBaseControllerExceptionUnauthorized('Authentication Failed. Check username/password');
            }
            
            return false;
        }
        
        $this->getResponse()->status = KHttpResponse::CREATED;
        $context->result = true;
        
        if ($data->return) {
            $_SESSION['return'] = $this->getService('com://site/people.filter.return')
                                       ->sanitize($data->return);             
            $context->url = base64UrlDecode($data->return);                      
        } else {
            $_SESSION['return'] = null;
        }
        
        return true;
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
        if ($this->getService('com:people.helper.person')->logout()) {
        	$context->response->status = KHttpResponse::NO_CONTENT;
        }
        
        $this->setRedirect(JRoute::_('index.php'));
    }

    /**
     * Logs in a user if an activation token is provided
     * 
     * @return boolean true on success
     */
    protected function _actionTokenlogin(KCommandContext $context)
    {
        if ($this->token == '') {
           throw new AnErrorException(array('No token is provided'), KHttpResponse::FORBIDDEN); 
           return false;
        }        
        
        $user = $this->getService('repos://site/users.user')
                     ->find(array(
                         'activation'=>$this->token
                         ));
        
        if (! $user) {
           throw new AnErrorException(array('This token is invalid'), KHttpResponse::NOT_FOUND); 
           return false;
        }
        
        $user->activation = '';
        $user->block = 0;
        $user->save();
        
        $person = $this->getService('repos://site/people.person')->find(array('userId'=>$user->id));
        
        if ($this->account_activate) {
            $person->enabled = 1;
            $person->save();
        }
        
        if ($this->reset_password){ 
            $context->response->setRedirect($person->getURL().'&get=settings&edit=account');
        }
        
        $context->append(array(
            'data'=>array(
               'username' => $user->username,
               'password' => $user->password,
               'remember' => true,
               'return' => null
               )
            )
        );
        
        return $this->execute('add', $context);
    }
    
	/**
     * Set the request information
     *
     * @param array An associative array of request information
     * 
     * @return LibBaseControllerAbstract
     */ 
    public function setRequest(array $request)
    {
    	parent::setRequest($request);
    	
    	if (isset($this->_request->return)) {	    
    		$return = $this->getService('com://site/people.filter.return')
    		               ->sanitize($this->_request->return);
    		$this->_request->return = $return;
    		$this->return = $return;
        }
    	
    	return $this;
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
        $url = JRoute::_($context->url);
        $context->response->setRedirect($url);
    }  
}