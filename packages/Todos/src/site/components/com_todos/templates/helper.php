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
 * @category	Anahita
 * @package	Com_Todos
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
		
		return $html->select('priority', array('options'=>$options, 'selected'=>$selected))->class('input-medium')->id('todo-priority');
	}
}