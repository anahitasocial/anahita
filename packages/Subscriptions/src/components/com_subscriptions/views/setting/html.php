<?php

/**
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Default Subscriptions Setting View (Profile View).
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsViewSettingHtml extends ComBaseViewHtml
{
    protected function _layoutDefault()
    {
        $subscription = null;

        if ($this->actor->hasSubscription(false)) {
            $subscription = $this->actor->subscription;
        }

        $this->set(array(
            'subscription' => $subscription,
        ));
    }

    protected function _layoutEdit()
    {
        $selectedPackageId = 0;

        $packages = $this->getService('repos:subscriptions.package')->getQuery()->fetchSet()->order('ordering');

        $endDate = new AnDate();

        if ($this->actor->hasSubscription(false)) {
            $selectedPackageId = $this->actor->subscription->package->id;

            $config = new KConfig(array(
                'date' => $this->actor->subscription->endDate,
           ));

            $endDate = new AnDate($config);
        }

        $this->set(array(
            'packages' => $packages,
            'selectedPackageId' => $selectedPackageId,
            'endDate' => $endDate,
        ));
    }
}
