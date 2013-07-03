<?php
/**
 * @version		Id
 * @category	Anahita_Todos
 * @package		Site
 * @subpackage  View
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

/**
 * Template Helper
 * 
 * @category	Anahita_Todos
 * @package		Site
 * @subpackage  Template
 */
class ComTodosTemplateHelper extends KTemplateHelperAbstract
{	
	
	/**
	 * Return a priority label
	 * 
	 * @param  ComTodosDomainEntityTodo
	 * @return string
	 */
	public function priorityLabel($todo)
	{
		switch(true)
		{
			case $todo->priority > 1 	: 
				$label = 'highest';
				break;
			case $todo->priority == 1	:
				$label = 'high';
				break;
			case $todo->priority == -1	:
				$label = 'low';
				break;
			case $todo->priority < -1	:
				$label = 'lowest';
				break;
			default	:
				$label = 'normal';
				break;
		}
		
		return $label;		
	}
	
	/**
	 * Renders todolist selector using an actor todolists 
	 *
	 * @param ComActorsDomainEntityActor $actor
	 * @param ComTodosDomainEntityTodolist $selected
	 */
	public function todolists($actor, $selected)
	{
		$html = $this->_template->getHelper('html');
						
		$this->getService('repos://site/todos.milestone');
		
		$todolists = $actor->todolists->order('title');
			
		if( count($todolists) == 0 )
			return JText::_('COM-TODOS-TODOLISTS-EMPTY-LIST-MESSAGE');
		
		$options[] 	= JText::_('COM-TODOS-SELECT-A-TODO-LIST');
		
		foreach($todolists as $todolist) 
			$options[$todolist->id] = $todolist->title;
		
		return $html->select('pid', array('options'=>$options, 'selected'=>$selected ? $selected->id : null))->class('input-xlarge');
	}
	
	/**
	 * Todo item priority list
	 * 
	 * @param  string $selected priority constant 
	 * @return string html priority list options
	 */	
	public function prioritylist($selected = null)
	{
		$html = $this->_template->getHelper('html');
		
		if(!$selected)
			$selected = ComTodosDomainEntityTodo::PRIORITY_NORMAL;

		$options = array(
			array(ComTodosDomainEntityTodo::PRIORITY_HIGHEST,	JText::_('COM-TODOS-TODO-PRIORITY-HIGHEST')),
			array(ComTodosDomainEntityTodo::PRIORITY_HIGH,		JText::_('COM-TODOS-TODO-PRIORITY-HIGH')),
			array(ComTodosDomainEntityTodo::PRIORITY_NORMAL,	JText::_('COM-TODOS-TODO-PRIORITY-NORMAL')),
			array(ComTodosDomainEntityTodo::PRIORITY_LOW,		JText::_('COM-TODOS-TODO-PRIORITY-LOW')),
			array(ComTodosDomainEntityTodo::PRIORITY_LOWEST,	JText::_('COM-TODOS-TODO-PRIORITY-LOWEST'))
		);
		
		return $html->select('priority', array('options'=>$options, 'selected'=>$selected))->class('input-medium');
	}
	
	/**
	 * Renders todolist selector using an actor milestones 
	 *
	 * @param ComActorsDomainEntityActor $actor
	 * @param ComTodosDomainEntityTodolist $selected
	 */
	public function milestones($actor, $selected)
	{
		$html = $this->_template->getHelper('html');
		
		$currentDate = new KDate();
		
		$this->getService('repos://site/todos.milestone');
		
		$milestones = $actor->milestones->where('endDate', '>=', $currentDate->getDate())->order('title', 'ASC');	

		if( count($milestones) == 0 )
			return JText::_('COM-TODOS-MILESTONES-EMPTY-LIST-MESSAGE');
		
		$options[] 	= JText::_('COM-TODOS-SELECT-A-MILESTONE');
		
		foreach($milestones as $milestone) 
			$options[$milestone->id] = $milestone->title;
		
		return $html->select('pid', array('options'=>$options, 'selected'=>$selected ? $selected->id : null))->class('input-xlarge');
	}	
}