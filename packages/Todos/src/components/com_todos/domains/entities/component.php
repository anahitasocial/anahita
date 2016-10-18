<?php

/**
 * Component object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTodosDomainEntityComponent extends ComMediumDomainEntityComponent
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'story_aggregation' => array('todo_disable,todo_add,todo_enable' => 'target'),
            'behaviors' => array(
                    'scopeable' => array('class' => 'ComTodosDomainEntityTodo'),
                    'hashtagable' => array('class' => 'ComTodosDomainEntityTodo'),
                ),
        ));

        parent::_initialize($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
        if ($mode == 'profile') {
            $gadgets->insert('todos-gadget-profile-todos', array(
                'title' => AnTranslator::_('COM-TODOS-GADGET-ACTOR-TODOS'),
                'url' => 'option=com_todos&view=todos&layout=gadget&oid='.$actor->id,
                'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                'action_url' => 'option=com_todos&view=todos&oid='.$actor->id,
            ));
        } else {
            $gadgets->insert('todos-gadget-profile-todos', array(
                'title' => AnTranslator::_('COM-TODOS-GADGET-DASHBOARD-TODOS'),
                'url' => 'option=com_todos&view=todos&layout=gadget&filter=leaders',
                'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                'action_url' => 'option=com_todos&view=todos&filter=leaders',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _setComposers($actor, $composers, $mode)
    {
        if ($actor->authorize('action', 'com_todos:todo:add')) {
            $composers->insert('todos-composer', array(
                'title' => AnTranslator::_('COM-TODOS-COMPOSER-TODO'),
                'placeholder' => AnTranslator::_('COM-TODOS-TODO-ADD'),
                'url' => 'option=com_todos&view=todo&layout=composer&oid='.$actor->id,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _setMenuLinks($actor, $menuItems)
    {
        $menuItems->insert('todos-todos', array(
            'title' => AnTranslator::_('COM-TODOS-MENU-ITEM-TODOS'),
            'url' => 'option=com_todos&view=todos&oid='.$actor->uniqueAlias,
        ));
    }
}
