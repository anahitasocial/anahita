<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_People
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Person Helper. Provides some helper functions suchs as creating a person object from a user.
 * 
 * @category   Anahita
 * @package    Lib_People
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibPeopleHelperPerson extends KObject
{	
	/**
     * Logs in a user
     * 
     * @param array   $user     The user as an array
     * @param boolean $remember Flag to whether remember the user or not
     * 
     * @return boolean
     */
	public function login(array $user, $remember = false)
    {   	
		$session = &JFactory::getSession();
    		
		// we fork the session to prevent session fixation issues
		$session->fork();   
        
		JFactory::getApplication()->_createSession($session->getId());
    		
    	// Import the user plugin group
		JPluginHelper::importPlugin('user');
		
    	$options = array();	    	
    	$results = JFactory::getApplication()->triggerEvent('onLoginUser', array($user, $options));
        
		foreach ($results as $result){
			if (
			    $result instanceof JException || 
			    $result instanceof Exception || 
			    $result === false
                ){
				return false;
            }
        }
		
    	//if remember is true, create a remember cookie that contains the ecrypted username and password
		if ($remember){
    		// Set the remember me cookie if enabled
			jimport('joomla.utilities.simplecrypt');
			jimport('joomla.utilities.utility');
				
			$key = JUtility::getHash(KRequest::get('server.HTTP_USER_AGENT','raw'));
				
			if ($key){
				$crypt = new JSimpleCrypt($key);				
				$cookie = $crypt->encrypt(serialize(array(
					'username' =>$user['username'],
					'password' =>$user['password']
				)));				
				$lifetime = time() + AnHelperDate::yearToSeconds();
				setcookie(JUtility::getHash('JLOGIN_REMEMBER'), $cookie, $lifetime, '/');
			}
		}
		
		return true;
    }
    
	/**
     * Deletes a session and logs out the viewer

     * @return boolean
     */
    public function logout()
    {
        setcookie(JUtility::getHash('JLOGIN_REMEMBER'), false, time() - AnHelperDate::dayToSeconds(30), '/');
        return JFactory::getApplication()->logout();
    }
}