<?php
/**
 * @version   	$Id: menubar.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright  	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license   	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Menubar 
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerToolbarMenubar extends KControllerToolbarAbstract
{
	/**
	 * Push the menubar into the view
	 * .
	 * @param	KEvent	A event object
	 */
    public function onBeforeControllerGet(KEvent $event)
    {   
        $event->caller->getView()->menubar = $this;
    }
 	
 	/**
     * Add a command
     * 
     * Disable the menubar only for singular views that are editable.
     *
     * @param   string	The command name
     * @param	mixed	Parameters to be passed to the command
     * @return  KControllerToolbarInterface
     */
    public function addCommand($name, $config = array())
    {
        parent::addCommand($name, $config);
        
        $controller = $this->getController();
        
        if($controller->isEditable() && KInflector::isSingular($controller->getView()->getName())) {
            $this->_commands[$name]->disabled = true;
        }
        
        return $this;
    }

    /**
	 * Get the list of commands
	 *
	 * Will attempt to use information from the xml manifest if possible
	 *
	 * @return  array
	 */
	public function getCommands()
	{
	    $name     = $this->getController()->getIdentifier()->name;
	    $package  = $this->getIdentifier()->package;
	    $manifest = JPATH_ADMINISTRATOR.'/components/com_'.$package.'/manifest.xml';

	    if(file_exists($manifest))
	    {
	        $xml = simplexml_load_file($manifest);
	        
	        if(isset($xml->administration->submenu)) 
	        {
	            foreach($xml->administration->submenu->children() as $menu)
	            {
	                $view = (string)$menu['view'];
	                  
	                if(!isset($menu['href'])) {
	                   $menu['href'] = 'index.php?option=com_'.$package.'&view='.$view;
	                }
	               
	                if(!isset($menu['active'])) {
	                    $menu['active'] = ($name == KInflector::singularize($view));
	                }
	                
	                $this->addCommand(JText::_((string)$menu), array(
	            		'href'   => JRoute::_($menu['href']),
	            		'active' => (string) $menu['active']
	                ));
	            }
	        }
	    }
	
	    return parent::getCommands();   
	}
}