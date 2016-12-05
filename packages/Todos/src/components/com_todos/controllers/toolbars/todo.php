<?php

/**
 * Todo Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTodosControllerToolbarTodo extends ComMediumControllerToolbarDefault
{
    /**
     * Called before list commands.
     */
    public function addListCommands()
    {
        $entity = $this->getController()->getItem();

        if ($entity->authorize('vote')) {
            $this->addCommand('vote');
        }

        if ($entity->authorize('edit')) {
            $this->addCommand('edit', array('layout' => 'form'))
            ->getCommand('edit')
            ->setAttribute('data-action', 'edit');

            $this->addCommand('enable', array('ajax' => true));
        }

        if ($entity->authorize('delete') && $this->getController()->filter != 'leaders') {
            $this->addCommand('delete');
        }
    }

    /**
     * Add Admin Commands for an entity.
     */
    public function addAdministrationCommands()
    {
        $this->addCommand('enable');
        parent::addAdministrationCommands();
    }

    /**
     * Enable Action for an entity.
     *
     * @param LibBaseTemplateObject $command Command Object
     */
    protected function _commandEnable($command)
    {
        $entity = $this->getController()->getItem();

        $label = AnTranslator::_('COM-TODOS-ACTION-'.strtoupper($entity->enabled ? 'disable' : 'enable'));

        $action = ($entity->enabled) ? 'disable' : 'enable';

        $command->append(array('label' => $label));

        if ($command->ajax) {
            $command
            ->href($entity->getURL().'&layout=list')
            ->setAttribute('data-action', $action);
        } else {
            $command
            ->href($entity->getURL().'&action='.$action)
            ->setAttribute('data-trigger', 'PostLink');
        }
    }
}
