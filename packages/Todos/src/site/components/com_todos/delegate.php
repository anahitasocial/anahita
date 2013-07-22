<?php
/**
 * @version		$Id
 * @category	Anahita_Todos
 * @package		Site
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

/**
 * Todos App
 * 
 * @category	Anahita_Pages
 * @package		Site
 */
class ComTodosDelegate extends ComAppsDomainDelegateDefault
{	
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
	
	/**
	 * Return a set of resources and type of operation on each resource
	 * 
	 * @return array
	 */
	public function getResources()
	{
		return array(
			'todo' => array('add','addcomment')
		);
	}
	
	/**
	 * Set the summerizers
	 *
	 * @param KCommandContext $context
	 * 
	 * @return void
	 */
	public function setStoryOptions($context)
	{
	    $context->append(array(
            'summarize' => array(
                'todo_disable,todo_add,todo_enable'=>'target'
            )
	    ));
	}

	/**
	 * On Destroy Nodes
	 *
	 * @param  KEvent $event
	 * @return void
	 */
	public function onDestroyNodes(KEvent $event)
	{
		$ids 	= KConfig::unbox($event->affected_node_ids);
		$this->_nullifyRecords($ids, 'todos_todos', 'open_status_change_by');
		$this->_nullifyRecords($ids, 'todos_milestones');
		$this->_nullifyRecords($ids, 'todos_todolists');
	}
}