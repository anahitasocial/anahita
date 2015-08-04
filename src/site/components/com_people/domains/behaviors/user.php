<?php

/**
 * User Behavior
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

class ComPeopleDomainBehaviorUser extends AnDomainBehaviorAbstract
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
            'attributes' => array(
                'userId' => array(
                    'column' => 'person_userid', 
                    'key' => true, 
                    'type' => 'integer', 
                    'default'=>mt_rand()
                    ),
            )
        ));
        
        parent::_initialize($config);
    }
    
    /**
     * before creating the person node, create the user object
     * 
     * @return boolean
     */
    protected function _beforeEntityInsert(KCommandContext $context)
    {
        $viewer = get_viewer();    
        $firsttime = !(bool) $this->getService('repos://site/users')
                                  ->getQuery(true)
                                  ->fetchValue('id');

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
        
        if ($this->password == '')
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
        
        // if this is the first user being added or 
        // (viewer is a super admin and she is adding another super admin)
        if ($firsttime || ($viewer->superadmin() && $this->userType == ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR))
        { 
            $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR);
        }
        // if viewer is an admin and she is going to add another admin
        elseif ($viewer->admin() && $this->userType == ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR)
        {
            $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR);
        }
        //default user type is registered
        else 
        {
           $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_REGISTERED);
        }
        
        $activationRequired = (bool) get_config_value('users.useractivation');                
        
        //if viewer is admin or activation is required, then create an activation token
        if ($viewer->admin() || $activationRequired)
        {
            $user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()));
            $user->set('block', '1');
        }
        
        if(! $user->save())
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
            // if viewer is super admin and she is updating another super admin's account    
            if ($viewer->superadmin() && $this->userType == ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR)
            { 
                $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR);
            }
            // if viewer is admin and she is updating another admin's account
            elseif ($viewer->admin() && $this->userType == ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR)
            {
                $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR);
            }
            else 
            {
               $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_REGISTERED);
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
     * Return the user object of the person
     * 
     * @return LibUsersDomainEntityUser
     */
    public function getUserObject()
    {
        $user = $this->getService('repos://site/users.user')->fetch(array('id'=>$this->userId));   

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
}    