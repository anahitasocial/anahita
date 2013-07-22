<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default App Dispatcher
 *
 * @category   Anahita
 * @package    Com_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDispatcher extends LibBaseDispatcherDefault
{			
	/**
	 * {@inheritdoc}
	 * 
	 * Guess the controller name based on the entity type
	 */
	public function setController($controller)
	{
		parent::setController($controller);
			
		if ( !$this->_controller instanceof KControllerAbstract ) 
		{
			$resource = clone $this->_controller;
			$resource->path = array('domain','entity');
			try 
			{
			    $repository = AnDomain::getRepository($resource);
			    $entity     = $repository->getClone();
                $default    = array('prefix'=>$entity, 'fallback'=>'ComBaseControllerService');		    			    
			} 
			catch(Exception $e)
			{
                $default    = array('default'=>array('ComBaseController'.ucfirst($this->_controller->name),'ComBaseControllerResource'));			    
			}	
            
            $default['identifier'] = $this->_controller;
            register_default($default);
		}
	}

	/**
	 * Draw the toolbar
	 * 
	 * @param KCommandContext $context The command context
	 * 
	 * @return string
	 */
	protected function _actionRender(KCommandContext $context)
	{
		if ( $context->result !== false ) {
			
 			$view = $this->getController()->getView();
 			        
	        //Set the document mimetype
    	    JFactory::getDocument()->setMimeEncoding($view->mimetype);
        
	        //Disabled the application menubar
	        if(!KInflector::isPlural($view->getName()) && !KRequest::has('get.hidemainmenu')) {
	            KRequest::set('get.hidemainmenu', 1);
	        } 
		}
		
		return parent::_actionRender($context);
	}
}