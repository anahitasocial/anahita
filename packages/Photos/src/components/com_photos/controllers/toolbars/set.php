<?php

/**
 * Set Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPhotosControllerToolbarSet extends ComMediumControllerToolbarDefault
{
    /**
     * Called after controller browse.
     *
     * @param KEvent $event
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        $filter = $this->getController()->filter;
        $actor = $this->getController()->actor;

        if (
              $this->getController()->canAdd() &&
              $filter != 'leaders' &&
              $actor->photos->getTotal() > 0
        ) {
            $this->addCommand('new');
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

        if ($entity->owner->authorize('administration')) {
            //organize photos
            $this->addCommand('organize', AnTranslator::_('COM-PHOTOS-ACTION-SET-ORGANIZE'))
                 ->getCommand('organize')
                 ->dataTrigger('Organize')
                 ->href(AnTranslator::_('option=com_photos&view=photos&layout=selector&oid='.$entity->owner->id.'&exclude_set='.$entity->id))
                 ->class('visible-desktop');

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
