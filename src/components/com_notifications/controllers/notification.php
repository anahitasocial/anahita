<?php

/**
 * Notification Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotificationsControllerNotification extends ComBaseControllerService
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'ownable',
                'serviceable' => array('except' => array('add', 'edit'))
            ),
            'request' => array('oid' => 'viewer'),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the count of new notifications.
     *
     * @return string
     */
    protected function _actionGetcount(KCommandContext $context)
    {
        $count = $this->actor->numOfNewNotifications();
        return $this->getView()->newNotifications($count)->display();
    }

    /**
     * Return a set of notification objects.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntitysetDefault
     */
    protected function _actionBrowse($context)
    {
        $this->actor->resetNotifications();

        if ($this->actor->eql(get_viewer())) {
            $title = AnTranslator::_('COM-NOTIFICATIONS-ACTORBAR-YOUR-NOTIFICATIONS');
        } else {
            $title = sprintf(AnTranslator::_('COM-NOTIFICATIONS-ACTORBAR-ACTOR-NOTIFICATIONS'), $this->actor->name);
        }

        $this->getToolbar('actorbar')->setTitle($title);

        $entities = parent::_actionBrowse($context);

        $entities
        ->where('notification.story_subject_id', '<>', $this->actor->id)
        ->clause('AND')
        ->where('notification.story_target_id', '=', $this->actor->id)
        ->where('notification.story_object_id', '=', $this->actor->id, 'OR')
        ->where('FIND_IN_SET('.$this->actor->id.', notification.subscriber_ids)', 'OR')
        ;

        if ($this->new) {
            $entities->id($this->actor->newNotificationIds->toArray());
        }

        //only zero the notifications if the viewer is the same as the
        //actor. prevents from admin zeroing others notifications
        if ($entities->count() > 0 && get_viewer()->eql($this->actor)) {
            //set the number of notification, since it's going to be
            KService::setConfig('com:viewer.html', array('data' => array('num_notifications' => $this->actor->numOfNewNotifications())));
            $this->registerCallback('after.get', array($this->actor, 'viewedNotifications'), $entities->toArray());
        }

        return $entities;
    }

    /**
     * Fake deleting a notification by removing the owner from the notification owners.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionDelete($context)
    {
        $this->actor->removeNotification($this->getItem());

        return $this->getItem();
    }

    /**
     * Checks if this controller can be executed by the viewer.
     *
     * @param string $action The action being executed
     *
     * @return bool
     */
    public function canExecute($action)
    {
        if (!$this->actor) {
            return false;
        }

        if (!$this->actor->isNotifiable()) {
            return false;
        }

        if ($this->actor->authorize('access') === false) {
            return false;
        }

        if ($this->actor->authorize('administration') === false) {
            return false;
        }

        return parent::canExecute($action);
    }
}
