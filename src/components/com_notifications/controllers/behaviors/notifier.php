<?php

/**
 * Publisher Behavior. Publishes stories after an action.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotificationsControllerBehaviorNotifier extends AnControllerBehaviorAbstract
{
    /**
     * Creates a notification.
     *
     * @param array $data Notification data
     *
     * @return ComNotificationDomainEntityNotification
     */
    public function createNotification($data = array())
    {
        $data = new KConfig($data);
        $viewer = $this->getService('com:people.viewer');

        $data->append(array(
          'component' => 'com_'.$this->_mixer->getIdentifier()->package,
          'subject' => $viewer
        ));

        $notification = $this->getService('repos:notifications.notification')->getEntity(array('data' => $data));

        $notification->removeSubscribers($viewer);

        return $notification;
    }
}
