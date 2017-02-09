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
class ComTopicsControllerToolbarActorbar extends ComMediumControllerToolbarActorbar
{
    /**
     * Before controller action.
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

        $title = AnTranslator::sprintf('COM-TOPICS-HEADER-TOPICS', $actor->name);

        $this->setTitle($title);

        $this->addNavigation(
            'topics',
            AnTranslator::_('COM-TOPICS-NAV-TOPICS'),
            array('option' => 'com_topics', 'view' => 'topics', 'oid' => $actor->id),
            $name == 'topic'
        );
    }
}
