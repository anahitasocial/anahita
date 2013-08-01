<?php

/** 
 * LICENSE: ##LICENSE##
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
 * Default Component Dispatcher 
 *
 * @category   Anahita
 * @package    Com_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDispatcherDefault extends LibBaseDispatcherComponent
{
    /**
     * If no view is set and there are no controller 
     * then set the controller to configuration 
     * 
     * (non-PHPdoc)
     * @see LibBaseDispatcherComponent::_actionGet()
     */
    protected function _actionGet(KCommandContext $context)
    {   
        //if there are no views then
        //lets redirect to configuration
        if ( !file_exists(JPATH_COMPONENT.'/views') &&
              file_exists(JPATH_COMPONENT.'/config.xml')
              && $context->request->get('view') != 'configurations'   
                 ) 
        {
            $query = $context->request->toArray();
            $query['view'] = 'configurations';
            $context->response->setRedirect(JRoute::_($query));
            return false;
        }

        parent::_actionGet($context);
    }
    
    /**
     * After dispatching legacy render the toolbar 
     * 
     * (non-PHPdoc)
     * @see LibBaseDispatcherComponent::_actionRenderlegacy()
     */
    protected function _actionRenderlegacy(KCommandContext $context)
    {
        parent::_actionRenderlegacy($context);
        global $mainframe;
        jimport( 'joomla.application.helper' );
        if (($path = JApplicationHelper::getPath( 'toolbar' )) && $mainframe->isAdmin())
        {
            // Get the task again, in case it has changed
            $task = JRequest::getString( 'task' );
        
            // Make the toolbar
            include_once( $path );
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