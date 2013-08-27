<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Process a notification and mail it out
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsControllerProcessor extends ComBaseControllerResource
{
    /**
     * Parser Template Helper
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
    
        $this->_parser  = $this->getService($config->parser);
    
        JFactory::getLanguage()->load('lib_anahita');
        JFactory::getLanguage()->load('com_actors');        
    }
    
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'parser'    => 'com://site/notifications.template.helper.parser',
            'behaviors' => array(
                'com://site/mailer.controller.behavior.mailer'
                    => $config->toArray()       
             )
        ));
        	
        parent::_initialize($config);
    }
        
    /**
     * Process an array of notifications
     * 
     * @param KCommandContext $context
     * 
     * @return void
     */
    protected function _actionProcess(KCommandContext $context)
    {
        $notifications = $this->getService('repos://site/notifications.notification')
            ->getQuery(true)
            ->status(ComNotificationsDomainEntityNotification::STATUS_NOT_SENT)
            ;
        
        if ( $this->id ) 
        {
            $ids = (array)KConfig::unbox($this->id);
            $notifications->id($ids);
        }
                
        $this->sendNotifications($notifications->fetchSet());
    }
    
    /**
     * Send a set of notifications
     *
     * @param array $notifications
     *
     * @return void
     */
    public function sendNotifications($notifications)
    {
        $space = $this->getService('anahita:domain.space');
        
        try
        {
            foreach($notifications as $notification)
                $notification->status = ComNotificationsDomainEntityNotification::STATUS_SENT;
            //change the notification status
            $space->commitEntities();
            //send the notification
            foreach($notifications as $notification) {
                $this->sendNotification($notification);
            }
        }
        catch(Exception $e) { }
         
        $space->commitEntities();
    }
    
    /**
     * Renders emails for a list of people
     *
     * @param array $config Config parameter
     *
     * @return array
     */
    protected function _renderMails($config)
    {
        $mails    = array();
        $config   = new KConfig($config);
        $settings     = $config->settings;
        $people       = $config->people;
        $notification = $config->notification;
        foreach($people as  $person)
        {
            $setting = $settings->{$person->id};
    
            if ( !$ret = $notification->shouldNotify($person, $setting) ) {
                $notification->removeSubscribers($person);
                continue;
            }
             
            $person->addNotification($notification);
             
            if ( $ret !== ComNotificationsDomainDelegateSettingInterface::NOTIFY_WITH_EMAIL ) {
                continue;
            }
             
            //since each owner revieces the mail, they are in fact the viewer
            //so we need to set the as viewer while processing the notification
            KService::set('com:people.viewer', $person);
             
            $notification->owner = $person;
            $data = new KConfig($this->_parser->parse($notification));
            $data->append(array(
                    'email_subject' => $data->title,
                    'email_title'	=> pick($data->email_subject, $data->title),
                    'email_body' 	=> $data->body,
                    'notification'	=> $notification
            ));
            if ( $notification->target && !$notification->target->eql($person)) 
            {
                $data->commands->insert('notification_setting', 
                    array('actor'=>$notification->target));
            }            
            $body = $this->renderMail(array(
                    'layout'   => false,
                    'template' => 'notification',
                    'data' => array(
                        'person'    => $person,
                        'commands'  => $data->commands,
                        'subject' => $notification->subject,
                        'title'	  => $data->email_title,
                        'body'    => $data->email_body
                    )
            ));
                
            $mails[] = array(
                  'subject' => $data->email_subject,
                  'body'    => $body,
                  'to'      => $person->email      
            );
        }
         
        return $mails;
    }
    
    /**
     * Send a set of notifications
     *
     * @param ComNotificationsDomainEntityNotification $notification Notification
     *
     * @return void
     */
    public function sendNotification($notification)
    {
        $people    = $this->getService('repos://site/actors.actor')->getQuery(true)->id($notification->subscriberIds->toArray())->fetchSet();
        $settings  = $this->getService('repos://site/notifications.setting')
                        ->getQuery(true,array('actor.id'=>$notification->target->id))
                        ->fetchSet();
        
        $settings  = AnHelperArray::indexBy($settings, 'person.id');
         
        $mails = $this->_renderMails(array('notification'=>$notification,'people'=>$people, 'settings'=>$settings));
        $debug = $this->getBehavior('mailer')->getTestOptions()->enabled;
        
        if ( $debug ) 
        {
            $recipients = array();
            foreach($mails as $i => $mail)
            {
                $recipients[] = $mail['to'];
                if ( $i < 3 )
                {
                    $body   = array();
                    $body[] = 'Subject   : '.$mail['subject'];
                    $body[] = $mail['body'];
                    $body   = implode('<br />', $body);
                    $bodies[] = $body;
                }
            }
            $bodies[] = 'Sending out '.count($mails).' notification mail(s)';
            $bodies[] = '<br /><br />'.implode('<br />',$recipients);            
            $mails    = array(array(
                 'subject'  => $notification->name,
                 'body'     => implode('<hr />', $bodies)     
            ));
        }
        foreach($mails as $mail) {
            $this->mail($mail);
        }
    }    
}