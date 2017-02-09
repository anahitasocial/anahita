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

        $viewer = $this->getController()->viewer;
        $actor = pick($this->getController()->actor, $viewer);
        $layout = pick($this->getController()->layout, 'default');
        $name = $this->getController()->getIdentifier()->name;

        $title = AnTranslator::sprintf('COM-NOTES-HEADER-NOTES', $actor->name);

        $this->setTitle($title);

        $this->addNavigation(
            'notes',
            AnTranslator::_('COM-NOTES-NAV-NOTES'),
            array('option' => 'com_notes', 'view' => 'notes', 'oid' => $actor->id),
            $name == 'note'
        );
    }
}
