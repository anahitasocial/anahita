<?php

/**
 * Package Controller.
 *
 * @category    Controller
 */
class ComSubscriptionsControllerPermissionOrder extends ComSubscriptionsControllerPermissionDefault
{
    /**
     * Authorize if viewer can browse.
     *
     * @return bool
     */
    public function canBrowse()
    {
        $viewer = get_viewer();

        if ($viewer->admin() || $viewer->eql($this->actor)) {
            return true;
        }

        return false;
    }

    /**
     * Authorize if viewer can read.
     *
     * @return bool
     */
    public function canRead()
    {
        $viewer = get_viewer();

        if ($viewer->admin() || $viewer->eql($this->actor)) {
            return true;
        }

        return false;
    }

    /**
     * Authorize if viewer can add.
     *
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * Authorize if viewer can edit.
     *
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }
}
