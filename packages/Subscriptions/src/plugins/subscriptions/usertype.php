<?php
/**
 * @version		$Id$
 * @category	Anahita_Apps
 * @package	 	Plugin
 * @subpackage  Subscriptions
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

/**
 * Subscription system plugins. Validates the viewer subscriptions
 * 
 * @category	Anahita_Apps
 * @package	 	Plugin
 * @subpackage  Subscriptions
 */
class PlgSubscriptionsUsertype extends PlgKoowaDefault 
{
	/**
	 * After subscription purchase
	 * 
	 * @param  KEvent $event
	 * @return void
	 */	
	public function onAfterExpire($event)
	{
		//change the usertype to registered
		$subscription = $event->subscription;
		$config		  = new KConfig($subscription->package->getPluginValues('usertype'));
		if ( $subscription ) {			
			$this->_changeUserType($subscription->person, 'Registered');
		}
	}
	
	/**
	 * After subscription purchase
	 * 
	 * @param  KEvent $event
	 * @return void
	 */	
	public function onAfterSubscribe($event)
	{
		$subscription = $event->subscription;
		$config		  = new KConfig($subscription->package->getPluginValues('usertype'));
		if ( $subscription && $config->change_gid ) {			
			$this->_changeUserType($subscription->person, KConfig::unbox($config->change_gid_to));
		}
	}
	
	/**
	 * Changes a usertype
	 * 
	 */
	protected function _changeUserType($person, $usertype)
	{
 		$user	   = JFactory::getUser($person->userId);
		$acl 	   = JFactory::getACL();
		
		if ( is_array($usertype) )
			$usertype = array_shift($usertype);
			
		if ( is_numeric($usertype) )
			$gid = (int) $usertype;
		else 
			$gid = $acl->get_group_id( '',$usertype, 'ARO' );

		//if not valid GID, then return Registered
		if ( !@$acl->get_group_data($gid) )
			$gid = $acl->get_group_id( '','Registered', 'ARO' );
				 		
 		$data	   = array('gid'=>$gid);
 		$user->bind($data);
 		$person->set('userType', $user->usertype);		
 		$user->save();
	}
}