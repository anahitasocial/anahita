<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Plugins
 * @subpackage System
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

jimport('joomla.plugin.plugin');

/**
 * Subscription system plugins. Validates the viewer subscriptions
 * 
 * @category   Anahita
 * @package    Anahita_Plugins
 * @subpackage System
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class plgSystemInvites extends JPlugin 
{
	/**
	 * onAfterRender handler
	 * 
	 * @return void
	 */
	public function onAfterRoute()
	{
		global $mainframe;
		
		if ( $mainframe->isAdmin() ) 
			return;	
			
		if ( $invite_token =
		         KRequest::get('get.invite_token','string') ) 
		{
		    $controller = KService::get('com://site/invites.controller.token', array(
		        'response' => KService::get('application.dispatcher')->getResponse() 
		    ));
		    try 
		    {
		        $controller->token($invite_token)->validate();		        
		        $controller->getResponse()->send();
		        exit(0);
		    } 
		    catch(KException $excetpion) {

		    }		    
		}
		
		$invite_token = KRequest::get('session.invite_token', 'string', null);
		
		if(!$invite_token)
			return;
			
		$token = KService::get('repos:invites.token')->fetch(array('value'=>$invite_token));
		
		if($token)
		{
			$usersConfig = &JComponentHelper::getParams( 'com_users' );
			$usersConfig->set( 'allowUserRegistration', true );
		}
	}
}