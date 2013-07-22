<?php 

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Notification text template helper class.
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsTemplateHelperNotifications extends KTemplateHelperAbstract
{
    /**
     * Group a set of notifications by date
     * 
     * @param array $notifications 
     * 
     * @return array
     */
    public function group($notifications)
    {
        $dates = array();
        $actor    = $this->getTemplate()->getView()->actor;
        $timezone = pick($actor->timezone, 0);
        foreach($notifications as $notification)
        {
            $current = AnDomainAttributeDate::getInstance()->addHours($timezone);
            $diff    = $current->compare($notification->createdOn->addHours($timezone));            
            
            if ( $diff <= AnHelperDate::dayToSeconds('1') )
            {
                if ( $current->day ==  $notification->createdOn->day )
                    $key = JText::_('LIB-AN-DATE-TODAY');
                else
                    $key = JText::_('LIB-AN-DATE-DAY');
            }
            else
                $key = $this->getTemplate()->renderHelper('date.format',$notification->createdOn, array('format'=>'%B %d'));
                
            if ( !isset($dates[$key]) ) {    
                $dates[$key] = array();   
            }
               
            $dates[$key][] = $notification;
        }
        
        return $dates;
    }
}