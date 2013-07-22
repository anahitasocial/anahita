<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Base Dispatcher
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class LibBaseDispatcherAbstract extends KDispatcherAbstract
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
        parent::_initialize($config);
        
        $config->request->append(array(
        	'format'	=> KRequest::format()        	
        ));
        
        if ( strpos($config->request->layout, '_') === 0 ) {
			unset($config->request->layout);
        }
        
        //Force the controller to the information found in the request
        if($config->request->view) {
            $config->controller = $config->request->view;
        }
    }
        
    /**
     * @see KDispatcherAbstract::_actionDispatch()
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        //Set the controller to the view passed in
        $data = KConfig::unbox($context->data);
        
        if( !empty($data) ) 
        	$this->setController($context->data);

        //Redirect if no view information can be found in the request
        if(!KRequest::has('get.view')) 
        {
            $url = clone(KRequest::url());
            $url->query['view'] = $context->data ? $context->data : $this->getController()->getIdentifier()->name;            
            JFactory::getApplication()->redirect($url);
            return;
        }
    		    			 
	    $action = KRequest::get('post.action', 'cmd', strtolower(KRequest::method()));
	    
	    $context->data = new KConfig();
	    	    
	    if(KRequest::method() != KHttpRequest::GET) {
            $context->data = KRequest::get(strtolower(KRequest::method()), 'raw', array());
        }
        
        $result = $this->getController()->execute($action, $context);
        	    
    	return $result;
    }
    
	/**
	 * Set the default controller to LibBaseControllerResource
	 * 
	 * @see KDispatcherAbstract::setController()
	 */
	public function setController($controller)
	{
		parent::setController($controller);
		
		if ( !$this->_controller instanceof KControllerAbstract ) {
			register_default(array('identifier'=>$this->_controller, 'default'=>'LibBaseControllerService'));
		}
	}
		
	/**
	 *  In Default App the forward condition is set to
	 * 	if context->result is a string or false don't forward
	 *  if request is HTTP or AJAX with context->redirect_ajax is set then redirect
	 *  ignore AJAX reuqest (in socialengien we never forward an ajax request)
	 * 
	 */
	public function _actionForward(KCommandContext $context)
	{		
	    //wierd bug in nooku. need to call request referere one
	    //time before using it
	    KRequest::referrer();
	    
		$redirect = $this->getController()->getRedirect();
		
		if ( $context->status_message ) {
			$redirect->message = $context->status_message;
		}
		
		if ( $context->getError() ) {
			$redirect->type = 'error';
		}
		

		if ( !isset($redirect['type']) ) {
			$redirect['type'] = 'success';	
		}
		
		if ( empty($redirect->url) ) {		  
			$redirect['url'] = KRequest::referrer();
		}
		
		//if a the result of disatched is string then
		//dispaly the returned value	
		if ( is_string($context->result) ) 
		{
			if ( KRequest::type() == 'HTTP') {
				JFactory::getApplication()->enqueueMessage($redirect['message'], $redirect['type']);
			} else {
				//set the redirect message in the header
				JResponse::setHeader('Redirect-Message', 	   $redirect['message']);
				JResponse::setHeader('Redirect-Message-Type',  $redirect['type']);				
			}
			//set the status to ok if there's a result
			$context->status = KHttpResponse::OK;
			return $context->result;
		}
				
		if (KRequest::type() == 'HTTP') {
			JFactory::getApplication()
					->redirect($redirect['url'], $redirect['message'], $redirect['type']);
		} else {
			JResponse::setHeader('Redirect-Message', 	   $redirect['message']);
			JResponse::setHeader('Redirect-Message-Type',  $redirect['type']);			
		}
	}

	/**
	 * Renders a controller view
	 * 
	 * @return string
	 */
	protected function _actionRender(KCommandContext $context)
	{
		if ( $context->result === false )
			return false;
			
		return parent::_actionRender($context);
	}
	
}