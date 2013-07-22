<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Person object. It's the main actor node that represents the social network users. A person can added 
 * applications to their profile  
 * 
 * Here's how to get a person object, set a property and save
 * <code>
 * //fetches a peron with $id
 * $person = KService::get('repos://site/people.person')->fetch($id); 
 * $person->name = 'James Bond';
 * $person->save();
 * </code>
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleDomainEntityPerson extends ComActorsDomainEntityActor 
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
			'attributes' 	=> array(
				'administratingIds' => array('type'=>'set', 'default'=>'set'),				
				'userId' 	 => array('column'=>'person_userid',	 	 'key'=>true, 'type'=>'integer'),
				'username'	 => array('column'=>'person_username',	 	 'key'=>true),
				'userType'	 => array('column'=>'person_usertype'),
				'email'		 => array('column'=>'person_useremail',	 	 'key'=>true, 'format'=>'email'),
				'givenName'  => array('column'=>'person_given_name',  'format'=>'string'),
				'familyName' => array('column'=>'person_family_name', 'format'=>'string'),
				'lastVisitDate' => array('type'=>'date', 'column'=>'person_lastvisitdate'),
				'language' => array('column'=>'person_language'),
				'timezone' => array('column'=>'person_time_zone'),
				'gender'   => array('column'=>'actor_gender')
			),
			'aliases' => array(
				'registrationDate' => 'creationTime',
				'aboutMe'		   => 'description'
			),		    
			'behaviors'	=>  array(
                'administrator',
                'notifiable',					
				'leadable'
			)
		));
				
		parent::_initialize($config);
        
        AnHelperArray::unsetValues($config->behaviors, array('administrable','enableable'));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _afterEntityInstantiate(KConfig $config)
	{
		$config->append(array('data'=>array(
			'author'     => $this,
		    'component' => 'com_people'		        
		)));
		
		parent::_afterEntityInstantiate($config);
	}
	
	/**
	 * Set the name, given name and family name of the person
	 * 
	 * @param string $name The name of the person
	 * 
	 * @return void
	 */
	public function setName($name)
	{
		$familyName = $givenName = '';
		
		if ( strpos($name, ' ') )			
			list($givenName, $familyName) 		= explode(' ',$name, 2);
		else 
			$givenName = $name;
			
		$this->set('givenName',  $givenName);		
		$this->set('familyName', $familyName);
		$this->set('name', $name);
	}
	
	/**
	 * Set the name, given name and family name of the person
	 * 
	 * @param string $name The name of the person
	 * 
	 * @return void
	 */
	public function setFamilyName($name)
	{
		$this->set('familyName', $name);
		$this->set('name', $this->givenName.' '.$this->familyName); 	
	}
	
	/**
	 * Set the name, given name and family name of the person
	 * 
	 * @param string $name The name of the person
	 * 
	 * @return void
	 */
	public function setGivenName($name)
	{
		$this->set('givenName', $name);
		$this->set('name', $this->givenName.' '.$this->familyName);
	}

	/**
	 * Return whether this person is a guest
	 * 
	 * @return boolean
	 */				
	public function guest()
	{
		return $this->userType == 'Guest';	
	}
		
	/**
	 * Return if the person user role is Administrator or Super Administrator
	 * 
	 * @return boolean
	 */
	public function admin()
	{
		return $this->userType == 'Administrator' || $this->userType == 'Super Administrator';
	}
	
}