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
 * Notification Setting Entity.
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Domains
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsDomainEntitySetting extends ComBaseDomainEntityEdge 
{
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
		    'behaviors' => array(
		        'dictionariable'
		    ),
			'aliases' => array(
			    'person' => 'nodeA',
                'actor'	 => 'nodeB'			
			)
		));
						
		parent::_initialize($config);
    }
	
	/**
	 * Set a notification value for a notitification type
	 *
	 * @param string   $type The type of the notification. 
	 * @param mixed	  $value The value of the notification setting.
	 * @param boolean $send_email Boolean flag to whether email the notifications to the user or not
	 * 
	 * @return ComNotificationsDomainEntitySetting
	 */
	public function setValue($type, $value, $send_email)
	{
	    $filter = $this->getService('koowa:filter.cmd');
	    $type   = $filter->sanitize($type);
	    $value  = $filter->sanitize($value);
	    
	    settype($send_email, 'boolean');
	    
	    if ( !in_range($type, 0, 2) )
	        $type = 1;
	    
	    $send_email = (bool) $send_email;
	    $this->__call('setValue', array($type, array('send_email'=>$send_email)));
	    return $this;
	}
	
	/**
	 * Gets the value of the setting. If the notificaiton type has not been set
	 * then NULL value is returned
	 *  
	 * @param string $type 	  The type of the notification
	 * @param mixed  $default The default value to return If the type is NULL
	 * 
	 * @return mixed The return value for the type. 
	 */
	public function getValue($type, $default = null)
	{
	    $ret  = $this->__call('getValue', array($type, $default));
	    if ( !isset($ret['value']) )
	        return $default;
	    return $ret['value'];
	}
	
	/**
	 * Returns whether the setting should send an email for a notification or not for a
	 * notification type
	 * 
	 * @param string $type 	  The type of the notification
	 * @param mixed  $default The default value to return If the type is NULL
	 * 
	 * @return boolean The boolean flag to whether send an email or not
	 */
	public function sendEmail($type, $default = false)
	{
	    $ret = $this->__call('getValue', array($type));
	    
	    if ( !isset($ret['send_email']) )
	        return (boolean)$default;

	    return (boolean)$ret['send_email'];
	}
}