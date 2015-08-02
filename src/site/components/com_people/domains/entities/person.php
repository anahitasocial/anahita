<?php

/** 
 * LICENSE: ##LICENSE##
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
    /*
     * Clear string passwrod.
     * 
     * @var string
     */
    protected $_password;
    
    /*
     * Hashtag regex pattern
     */
    const PATTERN_MENTION = '/@([A-Za-z][A-Za-z0-9_-]{3,})/';
    
    /*
     * Roles
     */ 
     const USERTYPE_GUEST = 'guest';
     const USERTYPE_REGISTERED = 'registered';
     const USERTYPE_ADMINISTRATOR = 'administrator';
     const USERTYPE_SUPER_ADMINISTRATOR = 'super-administrator';
    
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
			'attributes' => array(
				'administratingIds' => array('type'=>'set', 'default'=>'set'),				
				'userId' => array('column'=>'person_userid', 'key'=>true, 'type'=>'integer', 'default'=>rand()),
				'username' => array('column'=>'person_username', 'key'=>true, 'format'=>'username'),
				'userType' => array('column'=>'person_usertype', 'default'=>self::USERTYPE_REGISTERED),
				'email'	=> array('column'=>'person_useremail', 'key'=>true, 'format'=>'email'),
				'givenName'  => array('column'=>'person_given_name', 'format'=>'string'),
				'familyName' => array('column'=>'person_family_name', 'format'=>'string'),
				'lastVisitDate' => array('type'=>'date', 'column'=>'person_lastvisitdate'),
				'language' => array('column'=>'person_language'),
				'timezone' => array('column'=>'person_time_zone'),
				'gender'   => array('column'=>'actor_gender')
			),
			'aliases' => array(
				'registrationDate' => 'creationTime',
				'aboutMe' => 'description'
			),		    
			'behaviors'	=>  to_hash(array(
			    'describable' => array('searchable_properties'=>array('username')),
                'administrator',
                'notifiable',					
				'leadable'
			))
		));
				
		$config->behaviors->append(array('followable' => array('subscribe_after_follow'=>false)));
		
		parent::_initialize($config);
        
        AnHelperArray::unsetValues($config->behaviors, array('administrable'));
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
     * before creating the person node, create the user object
     * 
     * @return boolean
     */
    protected function _beforeEntityInsert(KCommandContext $context)
    {
        $viewer = get_viewer();    
        $firsttime = !(bool) $this->getService('repos://site/users')->getQuery(true)->fetchValue('id');

        //for now lets make the com_notes assigable to always
        if ($firsttime)
        {
            $component = KService::get('repos://site/components')->find(array('component' => 'com_notes'));
            
            if ($component) 
            {
                $component->setAssignmentForIdentifier('person', ComComponentsDomainBehaviorAssignable::ACCESS_ALWAYS);
            }
        }
        
        jimport('joomla.user.helper');
        $user = clone JFactory::getUser();
        
        $user->set('id', 0);
        $user->set('name', $this->name);
        $user->set('username', $this->username);
        $user->set('email', $this->email);
        
        if($this->password == '')
        {
            $password = JUserHelper::genRandomPassword();    
            $user->set('password', $password);   
            $user->set('password2', $password);
            $this->setPassword = $password;     
        } 
        else 
        {
            $user->password = $this->getPassword(true);    
        }
        
        $date =& JFactory::getDate();        
        $user->set( 'registerDate', $date->toMySQL());
        
        if ($firsttime || ($viewer->superadmin() && $this->userType == self::USERTYPE_SUPER_ADMINISTRATOR))
        { 
            $user->set('usertype', self::USERTYPE_SUPER_ADMINISTRATOR);
        }
        elseif ($viewer->admin() && $this->userType == self::USERTYPE_ADMINISTRATOR)
        {
            $user->set('usertype', self::USERTYPE_ADMINISTRATOR);
        }
        else 
        {
           $user->set('usertype', self::USERTYPE_REGISTERED);
        }
        
        $activationRequired = (bool) get_config_value('users.useractivation');                
        
        if ($viewer->admin() || $activationRequired)
        {
            $user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()));
            $user->set('block', '1');
        }
        
        if(!$user->save())
        {
            throw new RuntimeException('Unexpected error when saving user');
            return false;
        }
        
        $this->userId = $user->id;
        $this->userType = $user->usertype;
        $this->enabled = ($user->block) ? false : true;
        
        return true;
    }

    /**
     * Update the user object before updating the person node
     * 
     * @return boolean
     */
    protected function _afterEntityUpdate(KCommandContext $context)
    {
        jimport('joomla.user.helper');    
        $viewer = get_viewer();
        $user = clone JFactory::getUser( $this->userId );  
        
        if ($this->getModifiedData()->name) 
        {
            $user->set('name', $this->name);
        }  
        
        if ($this->getModifiedData()->username) 
        {
            $user->set('username', $this->username);   
        }
        
        if ($this->getModifiedData()->email) 
        {
            $user->set('email', $this->email);               
        }
        
        if ($this->getModifiedData()->enabled) 
        {
            $user->set('block', !$this->enabled);               
        }

        if ($this->getModifiedData()->userType)
        {
            if ($viewer->superadmin() && $this->userType == self::USERTYPE_SUPER_ADMINISTRATOR)
            { 
                $user->set('usertype', self::USERTYPE_SUPER_ADMINISTRATOR);
            }
            elseif ($viewer->admin() && $this->userType == self::USERTYPE_ADMINISTRATOR)
            {
                $user->set('usertype', self::USERTYPE_ADMINISTRATOR);
            }
            else 
            {
               $user->set('usertype', self::USERTYPE_REGISTERED);
            }
        }
        
        if (! empty($this->password) && $this->getModifiedData()->password) 
        {
            $user->set('password', $this->getPassword(true));
        }

        if (@$this->params->language) 
        {            
            $user->_params->set( 'language', $this->params->language );              
        }
               
        if (! $user->save()) 
        {
            throw new RuntimeException('Unexpected error when saving user');
            return false;
        }
        
        return true;
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
		
		if (strpos($name, ' '))	
        {		
		    list($givenName, $familyName) = explode(' ', $name, 2);
		}
		else
        {     
			$givenName = $name;
        }
        	
		$this->set('givenName', $givenName);		
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
	 * Return the username as unique alias
	 * 
	 * (non-PHPdoc)
	 * @see AnDomainEntityAbstract::__get()
	 */
	public function __get($key)
	{
		if ($key == 'uniqueAlias') 
		{
			return $this->username;
		}
		return parent::__get($key);
	}
	
    /**
     * Captures the password value when password is set through
     * magic methods
     * 
     * @{inheritdoc}
     */
    public function __set($key, $value)
    {
        if ($key == 'password' && !empty($value)) 
        {
           return $this->setPassword($value);
        }
        else
        {
           return parent::__set($key, $value);
        }
    }
    
    /**
     * Set a person account passwrod. This password is not stored in the database
     * and only used for validation. @see
     * <code>
     * $person->setPassword('somepassowrd')->validate() //will validate the password
     * </code> 
     * @param string $password Clear password
     * 
     * @return ComPeopleDomainEntityPerson
     */ 
    public function setPassword($password)
    {
    	//make sure the passowrd is set to an empty string 
    	if (empty($password)) 
    	{
    		$password = ' ';
    	}
        
        $this->_password = $password;
        return $this;
    }
    
    /**
     * Return a person URL
     * 
     * @param boolean $use_username A flag whether to use the username in the URL or not 
     * 
     * @return string
     */
    public function getURL($use_username = true)
    {
        $url = 'option=com_people&view=person&id='.$this->id;
        
        if ($use_username)
        {
            $url .= '&uniqueAlias='.$this->username;
        }
        
    	return $url;
    }
    
    /**
     * Return the clear password set for validation. If a hash is set to true
     * then it first hash the password and then return it
     * 
     * @param boolean $hash.
     * 
     * @return string
     */
    public function getPassword($hash = false)
    {
        $password = $this->_password;
        
        if ($hash) 
        {
            jimport('joomla.user.helper');
            $salt = JUserHelper::genRandomPassword(32);
            $crypt = JUserHelper::getCryptedPassword($password, $salt);
            $password = $crypt.':'.$salt;            
        }
        
        return $password;
    }
    
    /**
     * Return the user object of the person
     * 
     * @return LibUsersDomainEntityUser
     */
    public function getUserObject()
    {
    	//@TODO we should use a belongs to relationship for this
    	$user = $this->getService('repos://'.$this->getIdentifier()->application.'/users.user')
    		         ->fetch(array('id'=>$this->userId));	
    	
    	return $user;
    }
    
    /**
     * Return a juser object
     * 
     * @return boolean
     */
    public function getJUserObject()
    {
        $user = clone JFactory::getUser();
        
        if ($this->persisted()) 
        {         
            $user->set('id', $this->id);
        }
        
        $user->set('name', $this->name);
        $user->set('username', $this->username);
        $user->set('email', $this->email);
        
        if ($this->_password)
        {
            $user->set('password',  $this->getPassword());
            $user->set('password2', $this->getPassword());
            $user->set('password_clear', $this->getPassword());
        }
        
        $user->set('usertype', self::USERTYPE_REGISTERED);
        
        if (! $this->persisted()) 
        {
            $date =& JFactory::getDate();
            $user->set('registerDate', $date->toMySQL());
        }
        
        return $user;
    }
    
	/**
	 * Return whether this person is a guest
	 * 
	 * @return boolean
	 */				
	public function guest()
	{
		return $this->userType == self::USERTYPE_GUEST;	
	}
		
	/**
	 * Return if the person user role is Administrator or Super Administrator
	 * 
	 * @return boolean
	 */
	public function admin()
	{
		return $this->userType == self::USERTYPE_ADMINISTRATOR || $this->userType == self::USERTYPE_SUPER_ADMINISTRATOR;
	}	
    
    /**
     * return true if the person's role is super admin
     * 
     * @return boolean
     */
    public function superadmin()
    {
       return $this->userType == self::USERTYPE_SUPER_ADMINISTRATOR; 
    }
}