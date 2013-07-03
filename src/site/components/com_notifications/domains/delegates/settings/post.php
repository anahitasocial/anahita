<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Domains
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Setting delegate
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Domains
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsDomainDelegateSettingPost extends ComNotificationsDomainDelegateSettingDefault
{        
    /**
    * Checks with whether to notify a person or not
    *
    * @param ComPeopleDomainEntityPerson        	  $person       Person that notification being sent to
    * @param ComNotificationsDomainEntityNotification $notification The notification object
    * @param ComNotificationsDomainEntitySetting      $setting	    The setting object, it maybe NULL
    * 
    * @return int
    */
    public function shouldNotify($person, $notification, $setting)
    {
        //$notification->getValue('new_post', false);
        $should_notify = self::NOTIFY_WITH_EMAIL;
        
        if ( $setting && !$setting->sendEmail('posts', true) ) {
            $should_notify = self::NOTIFY;
        }
        
        return $should_notify;
    }
}