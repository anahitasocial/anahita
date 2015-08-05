<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * People Dispatcher
 *
 * @category   Anahita
 * @package    Com_People
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleDispatcher extends ComBaseDispatcherDefault
{   	
	/**
	 * Handles passowrd token before dispatching 
	 * 
	 * (non-PHPdoc)
	 * @see ComBaseDispatcherDefault::_actionDispatch()
	 */
	protected function _actionDispatch(KCommandContext $context)
	{
	    if (
	        $this->token && 
	        $this->getController()
	             ->getIdentifier()
	             ->name == 'person'
        )
	    {
	        if ($this->getController()->canRead()) 
	        {
	            $this->getController()->login();

	            if ($this->reset_password) 
	            {
	                $url = JRoute::_($this->getController()
	                                      ->getItem()
	                                      ->getURL().'&get=settings&edit=account&reset_password=1');
	                $this->getController()
	                     ->getResponse()
	                     ->location = $url;
	            }
	            
	            $this->getController()
	                 ->getResponse()
	                 ->send();
                     
	            exit(0);
	        }
	    }
	    	    
	    return parent::_actionDispatch($context);
	}
	
	/**
	 * If session throws LibBaseControllerExceptionUnauthorized exception
	 * that means the user has entere wrong credentials. In that case
	 * let the application handle the error 
	 * 
	 * (non-PHPdoc)
	 * @see ComBaseDispatcherDefault::_actionException()
	 */
	protected function _actionException(KCommandContext $context)
	{
	    if (
	        $context->data instanceof LibBaseControllerExceptionUnauthorized && 
	        $this->getController() instanceof ComPeopleControllerSession 
        )
	    {
	       $context->response->send();
	       exit(0);           
	    }
	    else
        {     
	       parent::_actionException($context);
        }   
	}
}