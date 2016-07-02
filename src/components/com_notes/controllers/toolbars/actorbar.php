<?php

/**
 * Actorbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotesControllerToolbarActorbar extends ComBaseControllerToolbarActorbar
{
    /**
     * Before _actionGet controller event.
     *
     * @param KEvent $event Event object
     *
     * @return string
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        parent::onBeforeControllerGet($event);

        if ($this->getController()->isOwnable() && $this->getController()->actor) {
            $actor = pick($this->getController()->actor, get_viewer());
            $this->setTitle(sprintf(JText::_('COM-NOTES-HEADER-NOTES'), $actor->name));
            $this->setActor($actor);
        }
    }
}
