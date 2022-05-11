<?php

/**
 * Set Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPhotosControllerToolbarSet extends ComMediumControllerToolbarDefault
{
    /**
     * Set the toolbar commands.
     */
    public function addToolbarCommands()
    {
        $entity = $this->getController()->getItem();

        if ($entity->authorize('vote')) {
            $this->addCommand('vote');
        }

        if ($entity->owner->authorize('administration')) {
            $this->addCommand('organize');
            $this->addAdministrationCommands();
        }

        if ($entity->authorize('subscribe') || $entity->subscribed(get_viewer())) {
            $this->addCommand('subscribe');
        }

        if ($entity->authorize('delete')) {
            $this->addCommand('delete');
        }
    }
}
