<?php

/**
 * Subscription Controller.
 *
 * @category	Controller
 */
class ComSubscriptionsControllerPermissionSignup extends LibBaseControllerPermissionDefault
{
    /**
     * Can login.
     *
     * @return bool
     */
    public function canLogin()
    {
        $subscriber_id = KRequest::get('session.subscriber_id', 'cmd');

        if ($subscriber_id) {
            $this->person = $this->getService('repos:people.person')->find($subscriber_id);
        }

        return !is_null($this->person);
    }

    /**
     * (non-PHPdoc).
     *
     * @see LibBaseControllerPermissionAbstract::canExecute()
     */
    public function _canExecute($action)
    {
        $viewer = get_viewer();

        if ($viewer->hasSubscription()) {
            $ret = $this->getItem()->authorize('upgradepackage');
        } else {
            $ret = $this->getItem()->authorize('subscribepackage');
        }

        return $ret;
    }
}
