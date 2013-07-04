<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Menubar Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerToolbarMenubar extends ComBaseControllerToolbarAbstract
{
    /**
     * Before Controller _actionRead is executed
     *
     * @param KEvent $event Dispatcher event 
     *
     * @return void
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        $this->getController()->menubar = $this;
        
        $title[] = strtoupper('COM-'.$this->getController()->getIdentifier()->package.'-'.$this->getController()->getIdentifier()->name.'-PAGE-HEADER');
        $title[] = strtoupper('COM-'.$this->getController()->getIdentifier()->package.'-HEADER');
        
        $this->setTitle(translate($title));
    }
    
    /**
	 * Adds a command to the menubar. A menubar command must always be used as means to navigation
	 * through different views
	 *
	 * @param 	string  	  $name   Command name
	 * @param   string  	  $label  Navigation label
	 * @param	string|array  $url    Navigation URL
	 * @param	boolean       $active Boolean to set if a navigation command is active
	 * 
	 * @return	ComBaseControllerToolbarMenubar
	 */
	public function addNavigation($name, $label, $url, $active = false)
	{
		if ( is_array($url) ) {
			$url = http_build_query($url);
		}
		
		$command = $this->addCommand($name, array(
			'label' => $label
		))->getCommand($name)
		  ->href($url);		
		
		if ( $active ) {
			$command->class('active',' ');
		}
		
		return $command;
	}
}