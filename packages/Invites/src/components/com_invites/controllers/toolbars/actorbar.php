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
class ComInvitesControllerToolbarActorbar extends ComBaseControllerToolbarActorbar
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
        $this->setActor(get_viewer());

        parent::onBeforeControllerGet($event);

        $data = $event->data;
        $viewer = get_viewer();
        $actor = $viewer;
        $layout = pick($this->getController()->layout, 'default');
        $name = $this->getController()->getIdentifier()->name;

        if ($name == 'connection') {
            $name = $this->getController()->service;
        }

        $this->setTitle(AnTranslator::sprintf('COM-INVITES-ACTOR-HEADER-'.strtoupper($name).'S', $actor->name));

        //create navigations
        $this->addNavigation(
            'email',
            AnTranslator::_('COM-INVITES-LINK-EMAIL'),
            'option=com_invites&view=email',
            $name == 'email'
          );

        if (ComConnectHelperApi::enabled('facebook')) {
            $this->addNavigation(
              'facebook',
              AnTranslator::_('COM-INVITES-LINK-FACEBOOK'),
              'option=com_invites&view=connections&service=facebook',
              $name == 'facebook'
            );
        }
    }
}
