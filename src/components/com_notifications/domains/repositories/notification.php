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
 * @subpackage Domain
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */


define('PROCESSOR_PATH', JPATH_BASE.'/components/com_notifications/process.php');

/**
 * Notification Repository
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Domain
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsDomainRepositoryNotification extends AnDomainRepositoryDefault 
{
    /**
     * After Insert command. Called after a notification is inserted. This method
     * tries to send the notification
     *
     * @param KCommandContext $context The command context
     * 
     * @return void
     */
    protected function _afterEntityInsert(KCommandContext $context)
    {
        parent::_afterEntityInsert($context);
        
        $use_cron = get_config_value('notifications.use_cron', false);
        
        if ( !$use_cron ) 
            exec_in_background('php '.PROCESSOR_PATH.' '.JPATH_BASE.' '.$context->entity->id);
           
    }
}