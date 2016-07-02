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
        return  $this->getOrder() instanceof ComSubscriptionsDomainEntityOrder &&
                $this->getOrder()->canProcess();
    }
}
