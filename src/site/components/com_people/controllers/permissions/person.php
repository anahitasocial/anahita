<?php

/** 
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller_Permission
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * People Permissions
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller_Permission
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleControllerPermissionPerson extends ComActorsControllerPermissionDefault
{ 
    /**
     * Flag to see whather registration is open or not
     * 
     * @var boolean
     */
    protected $_can_register;
    
    /**
     * Flag to whether an activation is required or not
     *
     * @var boolean
     */
    protected $_activation_required;
    
    /**
     * Viewer object
     * 
     * @var ComPeopleDomainEntityPerson
     */ 
    protected $_viewer;
        
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_can_register = $config->can_register;        
        $this->_activation_required = $config->activation_required;
        $this->_mixer->permission = $this;
    }
        
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
            'activation_required' => get_config_value('users.useractivation'),                
            'can_register' => (bool) get_config_value('users.allowUserRegistration', true)
        ));
    
        $this->_viewer = get_viewer();
    
        parent::_initialize($config);
    }

    /**
     * if a token is passed in the reqeust, then it allows reading  
     * 
     * (non-PHPdoc)
     * @see ComActorsControllerPermissionAbstract::canRead()
     */
    public function canRead()
    {    
        //if there's a 
        if ($this->token) 
        {
           $user = $this->getService('repos://site/users.user')
                        ->find(array('activation' => $this->token));
           
           if ($user) 
           {
               $this->setItem($this->getRepository()->find(array('userId' => $user->id)));
               $this->getItem()->enabled = true;                
               $user->activation = null;
               $user->block = false;
               $user->save();
           }
        }
        
        if ($this->getRequest()->get('layout') == 'signup')
        {     
            return $this->_viewer->guest();
        }
        
        if ($this->getRequest()->get('layout') == 'add')
        {     
            return $this->_viewer->admin();
        }

        return parent::canRead();
    }  
    
    /**
     * return true if viewer is an admin or a guest
     * 
     * @return boolean
     */
    public function canAdd()
    { 
        if ($this->_viewer->guest() || $this->_viewer->admin())
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * return true if viewer is an admin or the same as the item being edited
     * 
     * @return boolean
     */
    public function canEdit()
    {            
        if ($this->_viewer->admin() || $this->_viewer->eql($this->getItem()))
        {
            return true;
        }
        
        return false;
    }  

    /**
     * Return whether the action is required or not
     *
     * @return boolean
     */
    public function activationRequired()
    {
        return $this->_activation_required;
    }
    
    /**
     * Set if an activation is required
     * 
     * @param boolean $activation_required
     * 
     * @return void
     */
    public function setActivationRequired( $activation_required )
    {
        $this->_activation_required = $activation_required;
    }
    
    /**
     * Set if the controller allows to register
     * 
     * @param boolean $can_register The value whether the user can register or not 
     * 
     * @return void
     */
    public function setRegistrationOpen( $can_register )
    {
        $this->_can_register = $can_register;
        return $this;
    }
    
    /**
     * Return whether the registration is open or not
     * 
     * @return boolean
     */
    public function isRegistrationOpen()
    {
        return $this->_can_register;
    }
}