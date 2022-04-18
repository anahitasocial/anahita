<?php

/**
 * Notification Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.Anahita.io
 */
class ComNotificationsDomainEntityNotification extends ComBaseDomainEntityNode
{
    /**
     * Notification Status.
     */
    const STATUS_NOT_SENT = 0;
    const STATUS_SENT = 1;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'name' => array('required' => true),
                'type' => array('column' => 'body'),
                'creationTime' => array(
                    'default' => 'date',
                    'column' => 'created_on',
                ),
                'status' => array('default' => self::STATUS_NOT_SENT),
                'subscriberIds' => array(
                    'type' => 'set',
                    'default' => 'set',
                    'write' => 'private',
                    'required' => true,
                ),
            ),
            'behaviors' => array(
                  'serializable' => array(
                      'serializer' => 'com:stories.domain.serializer.story',
                  ),
                  'dictionariable',
            ),
            'relationships' => array(
                'object' => array(
                    'polymorphic' => true,
                    'type_column' => 'story_object_type',
                    'child_column' => 'story_object_id',
                ),
                'subject' => array(
                    'required' => true,
                    'parent' => 'com:actors.domain.entity.actor',
                    'child_column' => 'story_subject_id',
                ),
                'target' => array(
                    'required' => true,
                    'parent' => 'com:actors.domain.entity.actor',
                    'child_column' => 'story_target_id',
                ),
                'comment' => array(
                    'parent' => 'com:base.domain.entity.comment',
                    'child_column' => 'story_comment_id',
                ),
             ),
        ));

        parent::_initialize($config);
    }

    /**
     * Initializes the options for an entity after being created.
     *
     * @param 	object 	An optional AnConfig object with configuration options.
     */
    protected function _afterEntityInstantiate(AnConfig $config)
    {
        $data = $config->data;

        $data->append(array(
            'subscribers' => array()
        ));

        if ($data->object) {
            if (is($data->object, 'ComBaseDomainEntityComment')) {
                $data->comment = $data->object;
                $data->object = $data->comment->parent;
                $data->append(array(
                     'subscribers' => array($data->comment->author->id),
                ));

            } elseif (
                $data->object->isModifiable() && 
                !is($data->object, 'ComActorsDomainEntityActor')
            ) {
                $data->append(array(
                    'subscribers' => array($data->object->author->id),
                ));

            } elseif (is_person($data->object)) {
                $data->append(array(
                    'subscribers' => array($data->object->id),
                ));
            }

            if ($data->object->isOwnable()) {
                $data->target = $data->object->owner;
            }
        }

        if ($data->target && $data->target->isNotifiable()) {
            $data->append(array(
                'subscribers' => array($data->target->id),
            ));
        }

        parent::_afterEntityInstantiate($config);

        if ($config->data->subscribers) {
            $this->setSubscribers($config->data->subscribers);
        }
    }

    /**
     * Sets the type of the notification. If an array of configuration is passed, it will
     * store it as the notification configuration.
     *
     * @param string $type   The type of the notification
     * @param array  $config An array of configuration for the notification
     *
     * @return ComNotificationsDomainEntityNotification
     */
    public function setType($type, $config = array())
    {
        $this->set('type', $type);

        foreach ($config as $key => $value) {
            $this->setValue($key, $value);
        }

        return $this;
    }

    /**
     * Set a list of notifications subscribers.
     *
     * @param array $subscribers An array of Ids or person objects
     */
    public function setSubscribers($subscribers)
    {
        //flatten the array
        $subscribers = AnHelperArray::getValues(AnConfig::unbox($subscribers));
        $ids = array();

        foreach ($subscribers as $subscriber) {
            if (is($subscriber, 'AnDomainEntityAbstract')) {
                $ids[] = $subscriber->id;
            } else {
                $ids[] = $subscriber;
            }
        }

        $ids = array_unique($ids);

        if (! empty($ids)) {
            $this->set('subscriberIds', AnDomainAttribute::getInstance('set')->setData($ids));
        } else {
            $this->delete();
        }

        return $this;
    }

    /**
     * Removes an array of people or ids from the list of subscribers.
     *
     * @param ComActorsDomainEntityActor|array $subscribers An array of people or ids
     */
    public function removeSubscribers($subscribers)
    {
        $subscribers = AnConfig::unbox($subscribers);

        if (is($subscribers, 'AnDomainEntityAbstract')) {
            $subscribers = array($subscribers);
        } else {
            $subscribers = (array) $subscribers;
        }

        $ids = $this->subscriberIds->toArray();

        foreach ($subscribers as $subscriber) {
            $id = is($subscriber, 'AnDomainEntityAbstract') ? $subscriber->id : $subscriber;
            unset($ids[$id]);
        }

        $this->set('subscriberIds', AnDomainAttribute::getInstance('set')->setData($ids));

        //if there are no more subscriber then delete the notification
        if (empty($ids)) {
            $this->delete();
        }

        return $this;
    }

    /**
     * Checks with a setting delegate of the notification whether to notify a person or not.
     *
     * @param ComPeopleDomainEntityPerson         $person
     * @param ComNotificationsDomainEntitySetting $setting
     *
     * @return int
     */
    public function shouldNotify($person, $setting)
    {
        //if a person is not notifiable then return false
        if (! $person->isNotifiable()) {
            return false;
        }

        //check if the target allows access to the person
        if (! $this->target->allows($person, 'access')) {
            return false;
        }

        if (isset($this->object) && $this->object->isPrivatable() && !$this->object->allows($person, 'access')) {
            return false;
        }

        if ($this->type) {
            $delegate = $this->getService('com:notifications.domain.delegate.setting.'.$this->type);
            return $delegate->shouldNotify($person, $this, $setting);
        } else {
            return ComNotificationsDomainDelegateSettingInterface::NOTIFY_WITH_EMAIL;
        }
    }
}
