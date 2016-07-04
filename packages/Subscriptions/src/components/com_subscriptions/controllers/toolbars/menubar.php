<?php

/**
 * Menubar Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerToolbarMenubar extends ComBaseControllerToolbarMenubar
{
    /**
     * Before Controller _actionRead is executed.
     *
     * @param KEvent $event Dispatcher event
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        parent::onBeforeControllerGet($event);

        if (!get_viewer()->admin()) {
            return;
        }

        $name = $identifier = $this->getController()->getIdentifier()->name;

        //packages transactions
        $this->addNavigation(
            'nav-transactions',
            AnTranslator::_('COM-SUBSCRIPTIONS-TRANSACTIONS-MENU-ITEM'),
            route('option=com_subscriptions&view=orders'),
            ($name == 'order') ? true : false);

        //packages navigation
        $this->addNavigation(
            'nav-packages',
            AnTranslator::_('COM-SUBSCRIPTIONS-PACKAGES-MENU-ITEM'),
            route('option=com_subscriptions&view=packages'),
            ($name == 'package') ? true : false);

        //coupons navigation
        $this->addNavigation(
            'nav-coupons',
            AnTranslator::_('COM-SUBSCRIPTIONS-COUPONS-MENU-ITEM'),
            route('option=com_subscriptions&view=coupons'),
            ($name == 'coupon') ? true : false);

       //coupons navigation
        $this->addNavigation(
            'nav-vats',
            AnTranslator::_('COM-SUBSCRIPTIONS-VATS-MENU-ITEM'),
            route('option=com_subscriptions&view=vats'),
            ($name == 'vat') ? true : false);
    }
}
