<?php

/**
 * Actorbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsControllerToolbarActorbar extends ComBaseControllerToolbarActorbar
{
    /**
     * Set the header for the default actor.
     *
     * @todo very large function perhaps it should be done as command chain
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntitysetDefault
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        parent::onBeforeControllerGet($event);

        //just in case
        if (!$this->getController()->isIdentifiable()) {
            return;
        }

        //if vieweing one actor
        if ($item = $this->getController()->getItem()) {
            //for now render actor socialgraph bar here
            if ($this->getController()->get == 'graph') {
                $this->_configureGraphBar();
            } else {
                $this->setActor(null);

                $this->setTitle(sprintf(JText::_('COM-ACTORS-PROFILE-ACTORBAR-TITLE'), $item->getName()));
            }
        }
        //if viewing a list of actors related to another actor
        elseif ($this->getController()->isOwnable() && $this->getController()->actor) {
            $this->_configureBar();
        }
    }

    /**
     * Configure actorbar for when socialgraph is showing. This is temporary as the
     * socialgraph should be moved into its own component.
     */
    protected function _configureGraphBar()
    {
        $entity = $this->getController()->getItem();
        $viewer = get_viewer();
        $types = array();

        if ($entity->isFollowable()) {
            $types[] = 'Followers';
        }

        if ($entity->isLeadable()) {
            $types[] = 'Leaders';

            if (!$viewer->guest() && !$entity->eql($viewer)) {
                $types[] = 'CommonLeaders';
            }
        }

        if ($entity->authorize('administration', array('strict' => true))) {
            $types[] = 'Blockeds';
        }

        $filter = $this->getService('koowa:filter.cmd');

        foreach ($types as $type) {
            $label = array(strtoupper('COM-'.$this->getIdentifier()->package.'-NAV-LINK-SOCIALGRAPH-'.$type));
            $label[] = 'COM-ACTORS-NAV-LINK-SOCIALGRAPH-'.strtoupper($type);
            $cmd = strtolower($filter->sanitize($type));

            if ($cmd == 'followers' && $this->getController()->type == 'leadables') {
                $active = true;
            } elseif ($cmd === $this->getController()->type) {
                $active = true;
            } else {
                $active = false;
            }

            $this->addNavigation('navbar-'.$cmd, translate($label), $entity->getURL().'&get=graph&type='.$cmd, $active);
        }

        $title = array(strtoupper('COM-'.$this->getIdentifier()->package.'-NAV-TITLE-SOCIALGRAPH'));

        $title[] = 'COM-ACTORS-NAV-TITLE-SOCIALGRAPH';

        $this->setTitle(sprintf(translate($title), $entity->name));

        $this->setActor($entity);
    }

    /**
     * Configure actorbar for when a list of actor's related to another is showing.
     */
    protected function _configureBar()
    {
        $filters = array('following');

        if ($this->getController()->getRepository()->hasBehavior('administrable')) {
            $filters[] = 'administering';
        }

        $this->setActor($this->getController()->actor);

        foreach ($filters as $filter) {
            $label = array(
                strtoupper('COM-'.$this->getIdentifier()->package.'-NAV-FILTER-'.$filter),
                strtoupper('COM-ACTORS-NAV-FILTER-'.$filter),
            );

            $this->addNavigation('navbar-'.$filter, translate($label),
                    array(
                        'option' => $this->getController()->option,
                        'view' => $this->getController()->view,
                        'oid' => $this->getController()->actor->id, 'filter' => $filter,
                    ),
                    $this->getController()->filter == $filter);
        }

        $filter = pick($this->getController()->filter, 'following');

        $title = array(
            strtoupper('COM-'.$this->getIdentifier()->package.'-NAV-TITLE-'.$filter),
            strtoupper('COM-ACTORS-NAV-TITLE-'.$filter),
        );

        $this->setTitle(sprintf(translate($title), $this->getController()->actor->name));
    }
}
