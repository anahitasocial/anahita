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
	 * Synchronize a person and user 
	 * 
	 * @param ComPeopleDomainEntityPerson $person
	 * @param JUser	                      $user
	 * 
	 * @return void
	 */
	public function synchronizeWithUser($person, $user)
	{
		if ($person->userId != $user->id)
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
		if(is_object($id) && !$id->id)
		{
			return null;
		}
		
		$user = JFactory::getUser($id);
		
		if(!$user->id) 
			return null;
		
	    $query = $this->getService('repos://site/people.person')->getQuery()->disableChain()->userId($user->id);		
		
	    if($person = $query->fetch())
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
		if (!$user->id)
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
    	
		foreach($results as $result)
		{
			if ($result instanceof JException || $result instanceof Exception || $result === false)
				return false;
		}
		
    	//if remember is true, create a remember cookie that contains the ecrypted username and password
		if ($remember)
		{
    		// Set the remember me cookie if enabled
			jimport('joomla.utilities.simplecrypt');
			jimport('joomla.utilities.utility');
				
			$key = JUtility::getHash(KRequest::get('server.HTTP_USER_AGENT','raw'));
				
			if($key)
			{
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