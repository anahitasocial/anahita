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
     * This method should handle any login logic and report back to the subject
     *
     * @access	public
     * @param   array   holds the user data
     * @param 	array   array holding options (remember, autoregister, group)
     * @return	boolean	True on success
     * @since	1.5
     */
    public function onLoginUser($user, $options = array())
    {
        KRequest::set('session.invite_token', null);
    }
    
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