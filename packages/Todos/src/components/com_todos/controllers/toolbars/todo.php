<?php

/**
 * Todo Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
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
            $this->addCommand('edit');
            $this->addCommand('enable');
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
}
