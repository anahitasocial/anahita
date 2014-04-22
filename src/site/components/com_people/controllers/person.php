<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Person Controller
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleControllerPerson extends ComActorsControllerDefault
{	
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
					
		$this->registerCallback('after.add', array($this, 'notifyAdminsNewUser'));
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
			'behaviors'	=> array('validatable','com://site/mailer.controller.behavior.mailer')		    
		));
		
		parent::_initialize($config);
		
		AnHelperArray::unsetValues($config->behaviors, 'ownable');
		
        //if it's a person view , set the default id to person
        if ( $config->request->view == 'person' )
        {
            $config->append(array(
                    'request'    => array(
						'id' => get_viewer()->id
                    )
            ));
        }
	}
	
    /**
     * Hides the menubar title
     * {@inheritdoc}
     */
	protected function _actionGet(KCommandContext $context)
	{	  
        $this->getToolbar('menubar')->setTitle(null);
        
        if ( $this->modal ) {
            $this->getView()->layout('add_modal');
        }
        
		return parent::_actionGet($context);
	}

    /**
     * Deletes a person and all of their assets. It also logsout the person.
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntityAbstract
     */
    protected function _actionDelete(KCommandContext $context)
    {
    	parent::_actionDelete($context);
        
        $this->commit();        
        
        $userId = $this->getItem()->userId;
        
        JFactory::getUser($userId)->delete();  
		JFactory::getApplication()->logout($userId);                  
    }
    
    /**
     * Person add action creates a new person object.
     * 
     * @param KCommandContext $context Commaind chain context
     * 
     * @return AnDomainEntityAbstract
     */
    protected function _actionAdd(KCommandContext $context)
    {    	
        //we are not saving this person but just validating it
        $person = parent::_actionAdd($context);
        $data   = $context->data;        
        
        $person->userId = PHP_INT_MAX; //is assiged automatically
        
        
        //manually set the password to make sure there's a password
        
        $person->setPassword($data->password);
        
        //add the validations here
        $this->getRepository()->getValidator()
                ->addValidation('username','uniqueness')
                ->addValidation('email',   'uniqueness')
                ;
                
        if ( $person->validate() === false ) {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
        }

        $person->reset();
        
        $firsttime  = !(bool)$this->getService('repos://site/users')->getQuery(true)->fetchValue('id');
        $user       = clone JFactory::getUser();
        $authorize  =& JFactory::getACL();
                
        if ( $firsttime ) {
            
            //for now lets make the com_notes assigable to always
            $component = $this->getService('repos://site/components')
                    ->find(array('component'=>'com_notes'));
            if ( $component ) {
                $component
                    ->setAssignmentForIdentifier('person', ComComponentsDomainBehaviorAssignable::ACCESS_ALWAYS);
            }
            $datbase = $this->getService('koowa:database.adapter.mysqli');
            //joomla legacy. don't know what happens if it's set to 1
            $query = "INSERT INTO #__users VALUES (62, 'admin', 'admin', 'admin@example.com', '', 'Super Administrator', 0, 1, 25, '', '', '', '')";
            $datbase->execute($query);
            $query = "INSERT INTO #__core_acl_aro VALUES (10,'users','62',0,'Administrator',0)";
            $datbase->execute($query);
            $query = "INSERT INTO #__core_acl_groups_aro_map VALUES (25,'',10)";
            $datbase->execute($query);
            $user  =& JFactory::getUser();
            $user  = JUser::getInstance(62);
            $this->unregisterCallback('after.add', array($this, 'notifyAdminsNewUser'));
        } else {
            $user->set('id', 0);
            
            $config = &JComponentHelper::getParams( 'com_users' );
            $user->set('usertype', $config->get( 'new_usertype' ));
            $user->set('gid', $authorize->get_group_id( '', $config->get( 'new_usertype' ), 'ARO' ));
            
            if ( $this->activationRequired() )
            {
                jimport('joomla.user.helper');
                $user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
                $user->set('block', '1');
            }
        }

        $user->set('name', $person->name);
        $user->set('username', $person->username);
        $user->set('email', $person->email);
        $user->set('password', $person->getPassword(true));
        $date =& JFactory::getDate();
        $user->set('registerDate', $date->toMySQL());        
        
        $user->save();
        $person = $this->getRepository()->find(array('userId'=>$user->id));
        
        //if person is null then user has not been saved
        if ( !$person ) {
            throw new RuntimeException('Unexpected error when saving user');
        }
        
        //set the portrait image
        if ( $file = KRequest::get('files.portrait', 'raw') ) {
            $person->setPortraitImage(array('url'=>$file['tmp_name'], 'mimetype'=>$file['type']));
        }
                            
        //set the status
        $this->getResponse()->status  = KHttpResponse::CREATED; 
        
        $this->setItem($person);
        
        if ( !$person->enabled ) {
            $this->registerCallback('after.add', array($this, 'mailActivationLink'));
        }
        elseif ( $this->isDispatched() ) 
        {
            if ( $context->request->getFormat() == 'html' ) 
            {
                $context->response->status = 200;
                $this->registerCallback('after.add', array($this, 'login'));
            }
        }
                
        return $person;
        
    }
    
    /**
     * Edit a person's data and synchronize with the person with the user entity
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntityAbstract
     */
    protected function _actionEdit(KCommandContext $context)
    {        
        //add the validations here
        $this->getRepository()->getValidator()
                ->addValidation('username','uniqueness')
                ->addValidation('email',   'uniqueness')
                ;
                        
        $data   = $context->data;
        
        $person = parent::_actionEdit($context);
              
        //manually set the password to make sure there's a password
        if ( !empty($data->password) ) {
            $person->setPassword($data->password);
        }
                
        if ( $person->validate() === false ) {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
        }
        
        $user = JFactory::getUser($person->userId);
        
        if ( $person->getModifiedData()->name ) {
            $user->set('name', $person->name);
        }
        
        if ( $person->getModifiedData()->username ) {
            $user->set('username', $person->username);   
        }
        
        if ( $person->getModifiedData()->email ) {
            $user->set('email', $person->email);               
        }
        
        if ( !empty($data->password) ) {
            $user->set('password', $person->getPassword(true));
        }
        if ( @$data->params->timezone ) {            
            $user->_params->set('timezone', $data->params->timezone);              
        }

        if ( !$user->save() ) {
            throw new RuntimeException('Unexpected error when saving user');
            return false;               
        }
        
        if ( !$person->save() ) {
            throw new RuntimeException('Unexpected error when saving user');
        }
        
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
         
        return $person;      
    }
     
    /**
     * Mail an activation link
     *
     * @param KCommandContext $context The context parameter
     * 
     * @return void
     */    
    public function mailActivationLink(KCommandContext $context)
    {    	    	
		$person = $context->result;
		$this->user = $person->getUserObject();
		$this->mail(array(
    				'to' 		=> $this->user->email,
    				'subject'	=> JText::_('COM-PEOPLE-ACTIVATION-SUBJECT'),
    				'template'	=> 'account_activate'
		));
		$context->response->setHeader('X-User-Activation-Required', true);
		$this->setMessage(JText::sprintf('COM-PEOPLE-ACTIVATION-LINK-SENT', $this->user->name),'success');
		$context->response->setRedirect(JRoute::_('option=com_people&view=session'));
    }
    
    /**
     * Notify admins for a new user
     *
     * @param KCommandContext $context The context parameter
     *
     * @return void
     */    
    public function notifyAdminsNewUser(KCommandContext $context)
    {        
        $person = $context->result;
        $this->user = $person->getUserObject();
        $this->mailAdmins(array(            				
            'subject'	=> JText::sprintf('COM-PEOPLE-NEW-USER-NOTIFICATION-SUBJECT', $this->user->name),
            'template'	=> 'new_user'
        ));
    }
    
    /**
     * (non-PHPdoc)
     * @see ComActorsControllerAbstract::redirect()
     */
    public function redirect(KCommandContext $context)
    {
        if ( $context->action != 'add' ) {
            return parent::redirect($context);
        }
    }
    
    /**
     * Login the user after creating it
     *
     * @param KCommandContext $context The context parameter
     * 
     * @return void
     */
    public function login()
    {
    	$user = (array)JFactory::getUser($this->getItem()->userId);
    	$this->getService()->set('com:people.viewer', $this->getItem());
    	$controller = $this->getService('com://site/people.controller.session', array('response'=>$this->getResponse()));
    	
    	return $controller->login($user);
    }
    
    /**
     * Called before the setting page is displayed
     * 
     * @param KEvent $event
     * 
     * @return void
     */
    public function onSettingDisplay(KEvent $event)
    {   
        $tabs = $event->tabs;   
        if ( JFactory::getUser()->id == $event->actor->userId ) {     
            $tabs->insert('account',array('label'=>JText::_('COM-PEOPLE-SETTING-TAB-ACCOUNT')));                    
        } 
    }    
}