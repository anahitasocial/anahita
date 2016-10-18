<?php

/**
 * Actorbar.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerToolbarActorbar extends ComBaseControllerToolbarActorbar
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

        $name = $this->getController()->getIdentifier()->name;

        if ($name != 'order') {
            return;
        }

        $viewer = $this->getController()->viewer;
        $actor = $this->getController()->actor;

        if (!$actor) {
            return;
        }

        $this->setActor($actor);
        $this->setTitle(AnTranslator::sprintf('COM-SUBSCRIPTIONS-ACTOR-HEADER-TITLE-ORDER', $actor->name));

            //create navigations
        $this->addNavigation(
            'subscriptions-orders',
            AnTranslator::_('COM-SUBSCRIPTIONS-TRANSACTIONS-MENU-ITEM'),
            array('option' => 'com_subscriptions', 'view' => 'orders', 'oid' => $actor->id));
    }
}
