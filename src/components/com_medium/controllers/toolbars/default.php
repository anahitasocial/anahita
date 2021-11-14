<?php

/**
 * Default Medium Controller Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComMediumControllerToolbarDefault extends ComBaseControllerToolbarDefault
{
    /**
     * Before Controller _actionRead is executed.
     *
     * @param AnEvent $event
     */
    public function onBeforeControllerRead(AnEvent $event)
    {
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

        if ($entity->authorize('vote')) {
            $this->addCommand('vote');
        }

        if ($entity->authorize('subscribe') || ($entity->isSubscribable() && $entity->subscribed(get_viewer()))) {
            $this->addCommand('subscribe');
        }

        if ($entity->authorize('edit')) {
            $this->addCommand('edit');
        }

        if ($entity->isOwnable() && $entity->owner->authorize('administration')) {
            $this->addAdministrationCommands();
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

        if ($entity->authorize('vote')) {
            $this->addCommand('vote');
        }

        if ($entity->authorize('edit')) {
            $this->addCommand('edit');
        }

        if ($entity->authorize('delete')) {
            $this->addCommand('delete');
        }
    }

    /**
     * Add Admin Commands for an entity.
     */
    public function addAdministrationCommands()
    {
        $entity = $this->getController()->getItem();

        if ($entity->isOwnable() && $entity->owner->authorize('administration')) {
            if ($entity->isEnablable()) {
                $this->addCommand('enable');
            }

            if ($entity->isCommentable()) {
                $this->addCommand('commentstatus');
            }
        }
    }
}
