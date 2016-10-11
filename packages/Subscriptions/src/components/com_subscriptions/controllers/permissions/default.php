<?php

/**
 * Subscription Controller.
 *
 * @category    Controller
 */
class ComSubscriptionsControllerPermissionDefault extends LibBaseControllerPermissionDefault
{
    /**
     * Authorize if viewer can administer.
     *
     * @return bool
     */
    public function canAdminister()
    {
        $viewer = get_viewer();
        return $viewer->admin() ? true : false;
    }

    /**
     * Authorize if viewer can add.
     *
     * @return bool
     */
    public function canAdd()
    {
        return $this->canAdminister();
    }

    /**
     * Authorize if viewer can change.
     *
     * @return bool
     */
    public function canEdit()
    {
        return $this->canAdminister();
    }

    /**
     * Authorize if viewer can delete.
     *
     * @return bool
     */
    public function canDelete()
    {
        return $this->canAdminister();
    }
}
