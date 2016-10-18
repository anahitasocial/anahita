<?php

/**
 * Subscription Controller.
 *
 * @category		Controller
 */
class ComSubscriptionsControllerPermissionSubscription extends ComSubscriptionsControllerPermissionDefault
{
    /**
     * only admins can browse.
     */
    public function canBrowse()
    {
        return $this->canAdminister();
    }

    /**
     * Can't be guest.
     *
     * @return bool
     */
    public function canRead()
    {
        return !get_viewer()->guest();
    }

    /**
     * (non-PHPdoc).
     *
     * @see ComBaseControllerPermissionDefault::canAdd()
     */
    public function canAdd()
    {
        $order = $this->getOrder();

        if ($order instanceof ComSubscriptionsDomainEntityOrder && $order->canProcess()){
            return true;
        }

        return  false;
    }
}
