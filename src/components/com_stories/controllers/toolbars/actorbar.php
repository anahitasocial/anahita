<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Stories
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
 * @package    Com_Stories
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesControllerToolbarActorbar extends ComBaseControllerToolbarActorbar
{
    /**
     * Before _actionGet controller event
     *
     * @param  KEvent $event Event object 
     * 
     * @return string
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        parent::onBeforeControllerGet($event);
        
        if ( $this->getController()->isOwnable() && $this->getController()->actor ) 
        {
            $actor = pick($this->getController()->actor, get_viewer());
            $this->setTitle(sprintf(JText::_('COM-STORIES-HEADER-STORIES'), $actor->name));
            $this->setActor( $actor );
        }
    }    
}