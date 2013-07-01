<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
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
 * @package    Com_Subscriptions
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class ComSubscriptionsControllerToolbarMenubar extends ComBaseControllerToolbarMenubar
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
        
     	$menu = array(
                 'packages' => JText::_('AN-SB-PACKAGES'),
                 'people'   => JText::_('AN-SB-PEOPLE'),
                 'coupons'  => JText::_('AN-SB-COUPONS'),
                 'vats'     => JText::_('AN-SB-TAXES')
         );

         foreach($menu as $view => $label)
         {
         	$this->addCommand( $label, array(	
         		'active'=> ($view == $this->getController()->view), 
         		'href'=>JRoute::_('index.php?option=com_subscriptions&view='.$view
         	)));
         }       
     }     
 }