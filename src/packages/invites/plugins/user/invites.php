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
class plgUserInvites extends JPlugin 
{
	/**
	 * store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	public function onAfterStoreUser($user, $isnew, $succes, $msg)
	{		
		if ( !$isnew ) 
			return;	
			
		$invite_token = KRequest::get('session.invite_token', 'string', null);
		
		if(!$invite_token)
			return;
			
		KRequest::set('session.invite_token', null);
		
		$token = KService::get('repos:invites.token')->fetch(array('value'=>$invite_token));
		
		$token->incrementUsed()->save();
	}
}