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
 * Person Helper. Provides some helper functions suchs as creating a person object from a user. Or to
 * login/logoug a user
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
     * Logouts a person
     *
     * @param ComPeopleDomainEntityPerson $person  Person to logout
     * @param array                       $options Array of options
     * 
     * @return void
     */
    public function logout($person, $options = array())
    {
        //preform the logout action
        $mainframe = JFactory::getApplication();
        $error     = $mainframe->logout();
        $options   = new KConfig($options);
        $options->append(array(
              'return'  => 'index.php',
              'message' => ''              
        ));
        $return = $options->return;
        // Redirect if the return url is not registration or login
        if ( $return && !( strpos( $return, 'com_user' )) ) {
            $mainframe->redirect( $return, $options->message, $options->type );
        }
    }
    
	/**
	 * Logs in a person 
	 * 
	 * @param ComPeopleDomainEntityPerson $person
	 * @param array                       $options
	 * 
	 * @return void
	 */
	public function login($person, $options = array())
	{
		$user	 	 = (array) JFactory::getUser($person->userId);
		$application = JFactory::getApplication();
		if ( @$user['id'] )
		{
			$session = &JFactory::getSession();
	
			// we fork the session to prevent session fixation issues
			$session->fork();
			$application->_createSession($session->getId());
			
			// Import the user plugin group
			JPluginHelper::importPlugin('user');
	
			// OK, the credentials are authenticated.  Lets fire the onLogin event
			
			$results = $application->triggerEvent('onLoginUser', array($user, $options));
	
			/*
			 * If any of the user plugins did not successfully complete the login routine
			 * then the whole method fails.
			 *
			 * Any errors raised should be done in the plugin as this provides the ability
			 * to provide much more information about why the routine may have failed.
			 */
	
			if (!in_array(false, $results, true))
			{
				// Set the remember me cookie if enabled
				if (isset($options['remember']) && $options['remember'])
				{
					jimport('joomla.utilities.simplecrypt');
					jimport('joomla.utilities.utility');
	
					//Create the encryption key, apply extra hardening using the user agent string
					$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);
	
					$crypt = new JSimpleCrypt($key);
					$rcookie = $crypt->encrypt(serialize($credentials));
					$lifetime = time() + 365*24*60*60;
					setcookie( JUtility::getHash('JLOGIN_REMEMBER'), $rcookie, $lifetime, '/' );
				}
				
				KService::set('com:people.viewer', $person);
				
				if ( isset($options['return']) ) {
					$application->redirect($options['return']);
				}
				
				return true;
			}
		}
		
		// Trigger onLoginFailure Event
		$application->triggerEvent('onLoginFailure', array($user));


		// If silent is set, just return false
		if (isset($options['silent']) && $options['silent']) 
		{
			return false;
		}

		// Return the error
		return JError::raiseWarning('SOME_ERROR_CODE', JText::_('E_LOGIN_AUTHENTICATE'));				
	}
	
	/**
	 * Synchronize a person and user 
	 * 
	 * @param ComPeopleDomainEntityPerson $person
	 * @param JUser	                      $user
	 * 
	 * @return void
	 */
	public function synchronizeWithUser($person, $user)
	{
		if ( $person->userId != $user->id )
			return;

		$params = new JParameter($user->params);
			
		$person->setData(array(	
			'component'		=> 'com_people',		
			'name' 			=> $user->name ,			
			'username'		=> $user->username ,
			'email'			=> $user->email	   ,
			'userType'		=> $user->usertype ,
			'registrationDate'	=> AnDomainAttribute::getInstance('date')->setDate($user->registerDate),
			'lastVisitDate'		=> AnDomainAttribute::getInstance('date')->setDate($user->lastvisitDate),
			'language'		=> $params->get('language'),
			'timezone'		=> $params->get('timezone'),
		    'enabled'       => !$user->block
		), AnDomain::ACCESS_PROTECTED);
	}
	
	/**
	 * Tries to get a user, if not exist then it will try to create one
	 * 
	 * @param JUser|int $id
	 * 
	 * @return ComPeopleDomainEntityPerson|null 
	 */
	public function getPerson($id)
	{
		if ( is_object($id) )
		{
			$id = $id->id;
			
			if ( !$id )
				return null;
		}
		
		$user   = JFactory::getUser($id);
		
		if ( !$user->id ) return null;
		
	    $query  = $this->getService('repos://site/people.person')->getQuery()->disableChain()->userId($user->id);		
		$person = $query->fetch();
		
		if ( $person )
			return $person;
			
		$person = self::createFromUser($user);
		
		$person->saveEntity();		
		
		return $person;
	}
	
	/**
	 * Create a person actor node from the user object
	 * 
	 *  
	 * @param JUser $user
	 * @return void
	 */
	public function createFromUser($user)
	{
		if ( !$user->id )
			return null;
			
		$params = new JParameter($user->params);

		$person = $this->getService('repos://site/people.person')->getEntity()->setData(array(
			'component'		=> 'com_people',
			'name' 			=> $user->name ,
			'userId'		=> $user->id   ,
			'username'		=> $user->username ,
			'email'			=> $user->email	   ,
			'userType'		=> $user->usertype ,
			'registrationDate'	=> AnDomainAttribute::getInstance('date')->setDate($user->registerDate),
			'lastVisitDate'		=> AnDomainAttribute::getInstance('date')->setDate($user->lastvisitDate),
			'language'		=> $params->get('language') ,
			'timezone'		=> $params->get('timezone')	,
		    'enabled'       => !$user->block		        
		), AnDomain::ACCESS_PROTECTED);
			
		return $person;
	}
}