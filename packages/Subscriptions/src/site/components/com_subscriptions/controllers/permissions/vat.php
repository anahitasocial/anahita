<?php

/**
 * Vat Controller.
 *
 * @category    Controller
 */
class ComSubscriptionsControllerPermissionVat extends ComSubscriptionsControllerPermissionDefault
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
