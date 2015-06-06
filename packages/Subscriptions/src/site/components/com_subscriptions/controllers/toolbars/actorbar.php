<?php

/** 
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller_Toolbar
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Actorbar. 
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller_Toolbar
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
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
        parent::onBeforeControllerGet($event);    
        
        $name = $this->getController()->getIdentifier()->name;
        
        if( $name != 'order' )
        {
            return;
        }
        
        $viewer = $this->getController()->viewer;
        $actor = pick($this->getController()->actor, $viewer);

        $this->setActor( $actor );
        $this->setTitle( JText::sprintf( 'COM-SUBSCRIPTIONS-ACTOR-HEADER-TITLE-ORDER', $actor->name ) ); 
    }
}