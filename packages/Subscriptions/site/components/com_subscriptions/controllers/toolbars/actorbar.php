<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Actorbar. 
 *
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsControllerToolbarActorbar extends ComBaseControllerToolbarActorbar
{
    /**
     * Before controller action
     *
     * @param  KEvent $event Event object 
     * 
     * @return string
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        $name = $this->getController()->getIdentifier()->name;
        
        if ( $name != 'subscription' || $name != 'order') {
            return;    
        }
         
        $actor = get_viewer();
        
        $this->setActor($actor);
        $this->setTitle(JText::_('COM-SUBSCRIPTIONS-ACTOR-HEADER-TITLE-SUBSCRIPTION'));
        		
		$name     = $this->getController()->getIdentifier()->name;		        
		
		$this->addNavigation('package', 
									JText::_('COM-SUBSCRIPTIONS-NAV-SUBSCRIPTION'),
									'option=com_subscriptions&view=subscription', $name == 'subscription');
									
		$this->addNavigation('orders', 
									JText::_('COM-SUBSCRIPTIONS-NAV-ORDERS'),
									'option=com_subscriptions&view=orders', $name == 'order');
		
		return parent::onBeforeControllerGet($event);        
    }
}