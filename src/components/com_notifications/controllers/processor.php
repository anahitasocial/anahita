<?php

/**
 * Process a notification and mail it out.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotificationsControllerProcessor extends ComBaseControllerResource
{
    /**
     * Parser Template Helper.
     *
     * @var ComStoriesTemplateHelperParser
     */
    protected $_parser;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_parser = $this->getService($config->parser);

        $this->getService('anahita:language')->load('com_actors');
    }

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
            'parser' => 'com:notifications.template.helper.parser',
            'behaviors' => array(
                'com:mailer.controller.behavior.mailer' => $config->toArray()
             ),
        ));

        parent::_initialize($config);
    }

    /**
     * Process an array of notifications.
     *
     * @param KCommandContext $context
     */
    protected function _actionProcess(KCommandContext $context)
    {
        // run this no more than 1 minute
        //set_time_limit(60);

        $query = $this->getService('repos:notifications.notification')
                      ->getQuery(true)
                      ->status(ComNotificationsDomainEntityNotification::STATUS_NOT_SENT);

        if ($this->id) {
            $ids = (array) KConfig::unbox($this->id);
            $query->id($ids);
        }

        $notifications = $query->fetchSet();

        $this->sendNotifications($notifications);
    }

    /**
     * Send a set of notifications.
     *
     * @param array $notifications
     */
    public function sendNotifications($notifications)
    {
        $space = $this->getService('anahita:domain.space');

        foreach ($notifications as $notification) {
            $notification->status = ComNotificationsDomainEntityNotification::STATUS_SENT;
            $this->sendNotification($notification);
        }

        //change the notification status
        $space->commitEntities();
    }

    /**
     * Send a set of notifications.
     *
     * @param ComNotificationsDomainEntityNotification $notification Notification
     */
    public function sendNotification($notification)
    {
        $subscriberIds = $notification->subscriberIds->toArray();
        $query = $this->getService('repos:people.person')
                       ->getQuery(true)
                       ->id($subscriberIds);

        $people = $query->fetchSet();

        $settings = $this->getService('repos:notifications.setting')
                         ->getQuery(true, array('actor.id' => $notification->target->id))
                         ->fetchSet();

        $settings = AnHelperArray::indexBy($settings, 'person.id');

        $mails = $this->_renderMails(array(
                        'notification' => $notification,
                        'people' => $people,
                        'settings' => $settings
                    ));

        $this->mail($mails);
    }

    /**
     * Renders emails for a list of people.
     *
     * @param array $config Config parameter
     *
     * @return array
     */
    protected function _renderMails($config)
    {
        $mails = array();
        $config = new KConfig($config);
        $settings = $config->settings;
        $people = $config->people;
        $notification = $config->notification;

        foreach ($people as  $person) {

            $setting = $settings->{$person->id};

            if (! $ret = $notification->shouldNotify($person, $setting)) {
                $notification->removeSubscribers($person);
                continue;
            }

            $person->addNotification($notification);

            if ($ret !== ComNotificationsDomainDelegateSettingInterface::NOTIFY_WITH_EMAIL) {
                continue;
            }

            //since each owner revieces the mail, they are in fact the viewer
            //so we need to set the as viewer while processing the notification
            KService::set('com:people.viewer', $person);

            $notification->owner = $person;
            $data = new KConfig($this->_parser->parse($notification));

            $data->append(array(
                'email_subject' => $data->title,
                'email_title' => pick($data->email_subject, $data->title),
                'email_body' => $data->body,
                'notification' => $notification
            ));

            if ($notification->target && !$notification->target->eql($person)) {
                $data->commands->insert('notification_setting', array('actor' => $notification->target));
            }

            $body = $this->renderMail(array(
                        'template' => 'notification',
                        'layout' => false,
                        'data' => array(
                            'person' => $person,
                            'commands' => $data->commands,
                            'subject' => $notification->subject,
                            'title' => $data->email_title,
                            'body' => $data->email_body
                        )
                    ));

            if ($person->email != '' && filter_var($person->email, FILTER_VALIDATE_EMAIL)) {
                $mails[] = array(
                      'subject' => $data->email_subject,
                      'body' => $body,
                      'to' => $person->email
                );
            }
        }

        return $mails;
    }
}
