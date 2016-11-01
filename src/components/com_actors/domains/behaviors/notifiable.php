<?php

/**
 * Notifiable Behavior. Internal behavior that's used by the Com_Notifications
 * to store manage an actor's notifications.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsDomainBehaviorNotifiable extends AnDomainBehaviorAbstract
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'notificationIds' => array(
                    'type' => 'set',
                    'default' => 'set',
                    'write' => 'private'
                ),
                'newNotificationIds' => array(
                    'type' => 'set',
                    'default' => 'set',
                    'write' => 'private'
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Return a set of actor notifications.
     *
     * @return AnDomainEntitysetDefault
     */
    public function getNotifications()
    {
        $repository = $this->getService('repos:notifications.notification');

        return $repository->getQuery()
                ->status(ComNotificationsDomainEntityNotification::STATUS_SENT)
                ->id($this->notificationIds->toArray())
                ->toEntityset();
    }

    /**
     * Adds a new notification for the actor.
     *
     * @param ComNotificationsDomainEntityNotification $notification Notification
     */
    public function addNotification($notification)
    {
        $this->_mixer->getRepository()
        ->registerCallback('before.update', array($this, 'resetStats'), array($this->_mixer));

        $this->_mixer->getRepository()
        ->registerCallback('before.update', array($this, 'removeOldNotifications'), array($this->_mixer));

        $ids = clone $this->notificationIds;
        $ids[] = $notification->id;
        $this->set('notificationIds', $ids);

        $ids = clone $this->newNotificationIds;
        $ids[] = $notification->id;
        $this->set('newNotificationIds', $ids);
    }

    /**
     * Return the number of new notifications.
     *
     * @return int
     */
    public function numOfNewNotifications()
    {
        return $this->newNotificationIds->count();
    }

    /**
     * Return if a notification has been viewed by the actor.
     *
     * @param ComNotificationsDomainEntityNotification $notification Notification
     *
     * @return bool
     */
    public function notificationViewed($notification)
    {
        return ! $this->newNotificationIds->offsetExists($notification->id);
    }

    /**
     * Marks the notifications as read.
     *
     * @param array $notifications An array of notifications
     */
    public function viewedNotifications($notifications)
    {
        $notifications = (array) $notifications;

        $ids = clone $this->newNotificationIds;

        foreach ($notifications as $notification) {
            $ids->offsetUnset($notification->id);
        }

        $this->set('newNotificationIds', $ids);

        $this->save();
    }

    /**
     * Removes a notification.
     *
     * @param ComNotificationsDomainEntityNotification $notification Notification
     *
     * @return bool
     */
    public function removeNotification($notification)
    {
        $ids = clone $this->notificationIds;
        $ids->offsetUnset($notification->id);
        $this->set('notificationIds', $ids);

        $ids = clone $this->newNotificationIds;
        $ids->offsetUnset($notification->id);
        $this->set('newNotificationIds', $ids);

        $notification->removeSubscribers(array($this->_mixer));
    }

    /**
     * Performs a notifications clean up.
     */
    public function resetNotifications()
    {
        $this->resetStats(array($this->_mixer));
    }

    /**
     * Reset stats.
     *
     * @param array $actor An array of notifiable actors
     */
    public function resetStats($actors)
    {
        foreach ($actors as $actor) {
            $ids = $actor->notificationIds->toArray();
            $ids = $this->getService('repos:notifications.notification')->getQuery()->id($ids)->fetchValues('id');
            $actor->set('notificationIds', AnDomainAttribute::getInstance('set')->setData($ids));
            $new_ids = array();

            foreach ($actor->newNotificationIds as $id) {
                if (in_array($id, $ids)) {
                    $new_ids[] = $id;
                }
            }

            $actor->set('newNotificationIds', AnDomainAttribute::getInstance('set')->setData($new_ids));
        }
    }

    /**
     * Remove old notifications.
     *
     * @param array $actor An array of notifiable actors
     */
    public function removeOldNotifications($actors)
    {
        foreach ($actors as $actor) {
            $read_notifications = array_diff($actor->notificationIds->toArray(), $actor->newNotificationIds->toArray());
            $date = $this->getService('anahita:domain.attribute.date')->modify('-5 days');
            $query = $this->getService('repos:notifications.notification')
                          ->getQuery()
                          ->id($read_notifications)
                          ->creationTime($date, '<');

            $notifications = $query->fetchSet();

            foreach ($notifications as $notification) {
                $actor->removeNotification($notification);
            }
        }
    }
}
