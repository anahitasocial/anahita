<?php
/**
 * @version     $Id: dispatcher.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Dispatcher Class
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultDispatcher extends KDispatcherDefault implements KServiceInstantiatable
{ 
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    { 
        parent::_initialize($config);
        
        //Force the controller to the information found in the request
        if($config->request->view) {
            $config->controller = $config->request->view;
        }
    }
    
	/**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return KDispatcherDefault
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    { 
       // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
            
            //Add the factory map to allow easy access to the singleton
            $container->setAlias('dispatcher', $config->service_identifier);
        }
        
        return $container->get($config->service_identifier);
    }
    
    /**
     * Dispatch the controller and redirect
     * 
     * This function divert the standard behavior and will redirect if no view
     * information can be found in the request.
     * 
     * @param   string      The view to dispatch. If null, it will default to
     *                      retrieve the controller information from the request or
     *                      default to the component name if no controller info can
     *                      be found.
     *
     * @return  KDispatcherDefault
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        //Redirect if no view information can be found in the request
        if(!$this->getRequest()->view) 
        {
            $route    = $this->getController()->getView()->getRoute();   
            JFactory::getApplication()->redirect($route);
        }
       
        return parent::_actionDispatch($context);
    }
    
    /**
     * Set the mimetype of the document and hide the menu if required
     *
     * @return  KDispatcherDefault
     */
    protected function _actionRender(KCommandContext $context)
    {
        $view = $this->getController()->getView();
        
        //Set the document mimetype
        JFactory::getDocument()->setMimeEncoding($view->mimetype);
        
        //Disabled the application menubar
        if($this->getController()->isEditable() && KInflector::isSingular($view->getName())) {
            KRequest::set('get.hidemainmenu', 1);
        } 
   
        return parent::_actionRender($context);
    }
}