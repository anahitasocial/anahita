<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Todos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Component object
 *
 * @category   Anahita
 * @package    Com_Todos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTodosDomainEntityComponent extends ComMediumDomainEntityComponent
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'story_aggregation' => array('todo_disable,todo_add,todo_enable'=>'target')
        ));
    
        parent::_initialize($config);
    }

    /**
     * Return an array of permission object
     *
     * @return array
     */
    public function getPermissions()
    {
        $permissions = parent::getPermissions();
        unset($permissions['com://site/todos.domain.entity.todolist']);
        unset($permissions['com://site/todos.domain.entity.milestone']);
        return $permissions;
    }
        
	/**
	 * @{inheritdoc}
	 */
	protected function _setGadgets($actor, $gadgets, $mode)
	{
		if ( $mode == 'profile' )
		{
			$gadgets->insert('todos-gadget-profile-todos', array(
					'title' 		=> JText::_('COM-TODOS-GADGET-ACTOR-TODOS'),
					'url'   		=> 'option=com_todos&view=todos&layout=gadget&oid='.$actor->id,
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url'		=> 'option=com_todos&view=todos&oid='.$actor->id
			));
			 
			$gadgets->insert('todos-gadget-profile-milestones', array(
					'title' 		=> JText::_('COM-TODOS-GADGET-ACTOR-MILESTONES'),
					'url'   		=> 'option=com_todos&view=milestones&layout=gadget&oid='.$actor->id,
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url'		=> 'option=com_todos&view=milestones&oid='.$actor->id
			));
		}
		else
			$gadgets->insert('todos-gadget-profile-todos', array(
					'title' 		=> JText::_('COM-TODOS-GADGET-DASHBOARD-TODOS'),
					'url'   		=> 'option=com_todos&view=todos&layout=gadget&filter=leaders',
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url'		=> 'option=com_todos&view=todos&filter=leaders'
			));
	}
	
	/**
	 * @{inheritdoc}
	 */
	protected function _setComposers($actor, $composers, $mode)
	{
		if ( $actor->authorize('action','com_todos:todo:add') )
			$composers->insert('todos-composer', array(
					'title'        => JText::_('COM-TODOS-COMPOSER-TODO'),
					'placeholder'  => JText::_('COM-TODOS-TODO-ADD'),
					'url'          => 'option=com_todos&view=todo&layout=composer&oid='.$actor->id
			));
	}	
}