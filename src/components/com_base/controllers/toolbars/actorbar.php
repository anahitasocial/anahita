<?php

/**
 * Actor Bar. Specialized toolbar to provide in-app navigation within a context of an actor.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerToolbarActorbar extends ComBaseControllerToolbarMenubar
{
    /**
     * Actor.
     *
     * @return ComActorsDomainEntityActor
     */
    protected $_actor;

    /**
     * Before Controller _actionRead is executed.
     *
     * @param AnEvent $event
     */
    public function onBeforeControllerGet(AnEvent $event)
    {
        $this->getController()->actorbar = $this;

        //set the actor by default to the data actor or viewer
        if ($this->getController()->isOwnable() && $this->getController()->actor) {
            $this->setActor($this->getController()->actor);
        }
    }

    /**
     * Set an actor for the menubar.
     *
     * @param ComActorsDomainEntityActor $actor
     */
    public function setActor($actor)
    {
        $this->_actor = $actor;

        return $this;
    }

    /**
     * Return the actor.
     *
     * @return ComActorsDomainEntityActor
     */
    public function getActor()
    {
        return $this->_actor;
    }
}
