<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Todos
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
 * @package    Com_Todos
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTodosControllerToolbarActorbar extends ComMediumControllerToolbarActorbar
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
    
        $data 	= $event->data;
		$viewer = get_viewer();
		$actor	= pick($this->getController()->actor, $viewer);
		$layout = pick($this->getController()->layout, 'default');
		$name	= $this->getController()->getIdentifier()->name;
		
		if($this->getController()->filter == 'leaders')
		{
			$this->setTitle(JText::_('COM-TODOS-HEADER-LEADERS'));
			$this->setDescription(JText::_('COM-TODOS-HEADER-LEADERS-DESCRIPTION'));
		}
		else 
		{
			$this->setTitle(JText::sprintf('COM-TODOS-ACTOR-HEADER-'.strtoupper($name).'S', $actor->name));
			
			//create navigations
			$this->addNavigation('todos',
									JText::_('COM-TODOS-LINK-TODOS'),
									array('option'=>'com_todos', 'view'=>'todos', 'oid'=>$actor->id),
									$name == 'todo');
				
			$this->addNavigation('todoslists', 
									JText::_('COM-TODOS-LINK-TODO-LISTS'), 
									array('option'=>'com_todos', 'view'=>'todolists','oid'=>$actor->id), 
									$name == 'todolist');
	
			$this->addNavigation('milestones', 
									JText::_('COM-TODOS-LINK-MILESTONES'), 
									array('option'=>'com_todos', 'view'=>'milestones','oid'=>$actor->id), 
									$name == 'milestone');
		}
    }    
}