<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Topics
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
 * @package    Com_Topics
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTopicsControllerToolbarActorbar extends ComMediumControllerToolbarActorbar
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
    
        $viewer = $this->getController()->viewer;
        $actor	= pick($this->getController()->actor, $viewer);
        $layout = pick($this->getController()->layout, 'default');
        $name	= $this->getController()->getIdentifier()->name;
    
        if($this->getController()->filter == 'leaders')
        {
            $this->setTitle(JText::_('COM-TOPICS-HEADER-LEADERS-TOPICS'));
            $this->setDescription(JText::_('COM-TOPICS-HEADER-LEADERS-TOPICS-DESCRIPTION'));
        }
        else
        {
            $this->setTitle(JText::sprintf('COM-TOPICS-HEADER-TOPICS', $actor->name));
    
            $this->addNavigation('topics',
                    JText::_('COM-TOPICS-NAV-TOPICS'),
                    array('option'=>'com_topics', 'view'=>'topics', 'oid'=>$actor->id),
                    $name == 'topic');
        }
    }    
}