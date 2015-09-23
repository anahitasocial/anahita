<?php

/**
 * Package Controller.
 *
 * @category    Controller
 */
class ComSubscriptionsControllerPermissionPackage extends ComSubscriptionsControllerPermissionDefault
{
    /**
     * Authorize if viewer can read.
     *
     * @return bool
     */
    public function canRead()
    {
        return $this->canAdminister();
    }

    /**
     * Authorize if viewer can add subscriber.
     *
     * @return bool
     */
    public function canAddsubscriber()
    {
        return $this->canAdminister();
    }

    /**
     * Authorize if viewer can delete subscriber.
     *
     * @return bool
     */
    public function canDeletesubscriber()
    {
        return $this->canAdminister();
    }

    /**
     * Authorize if viewer can change subscription.
     *
     * @return bool
     */
    public function canChangesubscription()
    {
        return $this->canAdminister();
    }
}
