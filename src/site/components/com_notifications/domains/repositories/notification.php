<?php

/** 
 * LICENSE: ##LICENSE##
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
     * If set the true it wil try to send the notification righ
     * after it has been created through a background process.
     * This require the PHP environment to have access to the shell
     * 
     * @see exec_in_background()
     * 
     * @var boolean
     */
    protected $_send_after_insert;
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_send_after_insert = $config->send_after_insert;
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
            'send_after_insert' => !get_config_value('notifications.use_cron', false)
        ));
    
        parent::_initialize($config);
    }
        
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

        if ( $this->_send_after_insert ) {
            exec_in_background('php '.JPATH_BASE.'/index.php '.PROCESSOR_PATH.' id='.$context->entity->id);
        }
    }
}