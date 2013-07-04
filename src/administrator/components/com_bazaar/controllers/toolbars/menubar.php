<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Bazar
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Subscription controller menubar
 * 
 * @category   Anahita
 * @package    Com_Bazar
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class ComBazaarControllerToolbarMenubar extends ComBaseControllerToolbarMenubar
 {     
     /**
      * Add subscription menu bar
      * .
      * @param KEvent $event The event object
      * 
      * @return void
      */
     public function onAfterControllerBrowse(KEvent $event)
     {     
        if ( JDEBUG )
        {
            $this->getController()->getToolbar('menubar')->addCommand(JText::_('Bazaar'), array('href'=>JRoute::_('index.php?option=com_bazaar&view=apps')));                
        }
     } 
     
    /**
     * SOnly add the parameter command if JDEBUG is on
     *
     * @return void
     */
    public function addParameterCommand()
    {
        if ( JDEBUG ) {   
            return parent::addParameterCommand();
        }
    }         
 }