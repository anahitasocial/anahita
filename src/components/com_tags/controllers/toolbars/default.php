<?php

/**
 * Default Tag Controller Toolbar.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComTagsControllerToolbarDefault extends ComBaseControllerToolbarDefault
{
    /**
     * Before Controller _actionRead is executed.
     *
     * @param AnEvent $event
     */
    public function onBeforeControllerGet(AnEvent $event)
    {
        parent::onBeforeControllerGet($event);

        if ($this->getController()->getItem()) {
            $this->addToolbarCommands();
        }
    }

    /**
     * Set the toolbar commands.
     */
    public function addToolbarCommands()
    {
        $entity = $this->getController()->getItem();

        if ($entity->authorize('edit')) {
            $this->addCommand('edit');
        }

        if ($entity->authorize('delete')) {
            $this->addCommand('delete');
        }
    }

    /**
     * Called before list commands.
     */
    public function addListCommands()
    {
        $entity = $this->getController()->getItem();

        if ($entity->authorize('edit')) {
            $this->addCommand('edit');
        }

        if ($entity->authorize('delete')) {
            $this->addCommand('delete');
        }
    }
}
