<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Mod_Actor
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Actor Module
 *
 * @category   Anahita
 * @package    Mod_Actor
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ModActorHtml extends ModBaseHtml
{
    /**
     * Default Layout
     *
     * @return
     */
	protected function _layoutDefault()
	{
    	$this->commands = $commands = new LibBaseTemplateObjectContainer();
    	
    	$viewer        = $this->viewer;
    	$actor         = $this->actor;
    	
        if ( $viewer->following( $actor ) ) 
        {
            $commands->insert('follow', array('label'=>JTEXT::_('MOD-ACTOR-ACTION-UNFOLLOW')))
                ->href($actor->getURL().'&action=unfollow')
                ->class('btn')->dataTrigger('Submit');
    	} 
    	elseif ( $actor->authorize('follower') || $viewer->guest() ) 
    	{
    	    $commands->insert('follow',array('label'=>JTEXT::_('MOD-ACTOR-ACTION-FOLLOW')))
    	    ->href($actor->getURL().'&action=follow')
    	    ->class('btn btn-primary')
    	    ->dataTrigger('Submit');
    	}
	}
}