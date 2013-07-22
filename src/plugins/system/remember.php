<?php
/**
* @version		$Id: remember.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! System Remember Me Plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class plgSystemRemember extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemRemember(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onAfterInitialise()
	{
		global $mainframe;

		// No remember me for admin
		if ($mainframe->isAdmin()) {
			return;
		}

		$user = &JFactory::getUser();
		if (!$user->get('gid'))
		{
			jimport('joomla.utilities.utility');
			$hash = JUtility::getHash('JLOGIN_REMEMBER');

			if ($str = JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
			{
				jimport('joomla.utilities.simplecrypt');

				//Create the encryption key, apply extra hardening using the user agent string
				// Since we're decoding, no UA validity check is required.
				$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

				$crypt	= new JSimpleCrypt($key);
				$str	= $crypt->decrypt($str);

				$cookieData = @unserialize($str);
                // Deserialized cookie could be any object structure, so make sure the 
                // credentials are well structured and only have user and password.
                $credentials = array();
                if (!is_array($credentials)) {
                    return;
                }
                if (!isset($cookieData['username']) || !is_string($cookieData['username'])) {
                    return;
                }
                $credentials['username'] = JFilterInput::clean($cookieData['username'], 'username');
                if (!isset($cookieData['password']) || !is_string($cookieData['password'])) {
                    return;
                }
                $credentials['password'] = JFilterInput::clean($cookieData['password'], 'string');

				if (!$mainframe->login($credentials, array('silent' => true))) {
					// Clear the remember me cookie
					setcookie( JUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, '/' );
				}
			}
		}
	}
}