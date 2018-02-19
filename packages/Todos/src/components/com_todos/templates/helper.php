<?php

/**
 * Template Helper.
 *
 * @category	Anahita
 */
class ComTodosTemplateHelper extends LibBaseTemplateHelperAbstract
{
    /**
     * Return a priority label.
     *
     * @param  ComTodosDomainEntityTodo
     *
     * @return string
     */
    public function priorityLabel($todo)
    {
        switch (true) {
            case $todo->priority > 1:
                $label = 'highest';
                break;
            case $todo->priority == 1:
                $label = 'high';
                break;
            case $todo->priority == -1:
                $label = 'low';
                break;
            case $todo->priority < -1:
                $label = 'lowest';
                break;
            default:
                $label = 'normal';
                break;
        }

        return $label;
    }

    /**
     * Todo item priority list.
     *
     * @param string $selected priority constant
     *
     * @return string html priority list options
     */
    public function prioritylist($selected = null)
    {
        $html = $this->_template->getHelper('html');

        if (!$selected) {
            $selected = ComTodosDomainEntityTodo::PRIORITY_NORMAL;
        }

        $options = array(
            array(ComTodosDomainEntityTodo::PRIORITY_HIGHEST,    AnTranslator::_('COM-TODOS-TODO-PRIORITY-HIGHEST')),
            array(ComTodosDomainEntityTodo::PRIORITY_HIGH,        AnTranslator::_('COM-TODOS-TODO-PRIORITY-HIGH')),
            array(ComTodosDomainEntityTodo::PRIORITY_NORMAL,    AnTranslator::_('COM-TODOS-TODO-PRIORITY-NORMAL')),
            array(ComTodosDomainEntityTodo::PRIORITY_LOW,        AnTranslator::_('COM-TODOS-TODO-PRIORITY-LOW')),
            array(ComTodosDomainEntityTodo::PRIORITY_LOWEST,    AnTranslator::_('COM-TODOS-TODO-PRIORITY-LOWEST')),
        );

        return $html->select('priority', array('options' => $options, 'selected' => $selected))->class('input-medium')->id('todo-priority');
    }
}
