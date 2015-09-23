<?php

/**
 * Coupon Controller.
 *
 * @category    Controller
 */
class ComSubscriptionsControllerPermissionCoupon extends ComSubscriptionsControllerPermissionDefault
{
    /**
     * Authorize if viewer can Browse.
     *
     * @return bool
     */
    public function canBrowse()
    {
        return $this->canAdminister();
    }

    /**
     * Authorize if viewer can read.
     *
     * @return bool
     */
    public function canRead()
    {
        return $this->canAdminister();
    }
}
