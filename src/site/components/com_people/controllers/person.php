<?php

/**
 * Person Controller
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3
 * @link       http://www.GetAnahita.com
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
        $this->registerCallback('after.add', array($this, 'mailActivationLink'));
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
            'behaviors' => array('validatable', 'com://site/mailer.controller.behavior.mailer')         
        ));
        
        parent::_initialize($config);
        
        AnHelperArray::unsetValues($config->behaviors, 'ownable');
    }
    
    /**
     * Post service
     * 
     * @param KCommandContext $context Commaind chain context
     * 
     * @return AnDomainEntityAbstract
     */
    protected function _actionPost(KCommandContext $context)
    {
        $data = $context->data;    
        
        $userTypes = array(
            ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR,
            ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR,
            ComPeopleDomainEntityPerson::USERTYPE_REGISTERED
        );
            
        if (!$this->getItem()->authorize('changeUserType') || !in_array($data->userType, $userTypes)){
            $data->usertype = ComPeopleDomainEntityPerson::USERTYPE_REGISTERED;
        }

        return parent::_actionPost($context);
    }
    
    /**
     * Person add action creates a new person object.
     * 
     * @param KCommandContext $context Commaind chain context
     * 
     * @return AnDomainEntityAbstract
     */ 
    protected function _actionAdd(KCommandContext  $context)
    {
        $data = $context->data; 
        $viewer = get_viewer();
        $person = parent::_actionAdd($context);
        $redirectUrl = 'option=com_people';
        
        $this->getRepository()
        ->getValidator()
        ->addValidation('username','uniqueness')
        ->addValidation('email', 'uniqueness');
        
        if ($person->validate() === false) {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
            return false;
        }

        if ($viewer->admin()) {
            $redirectUrl .= '&view=people';
            if ($person->admin()) {
               $this->registerCallback( 'after.add', array($this, 'mailAdminsNewAdmin')); 
            }
        } else { 
            $context->response->setHeader('X-User-Activation-Required', true);
            $this->setMessage(JText::sprintf('COM-PEOPLE-PROMPT-ACTIVATION-LINK-SENT', $person->name), 'success');
            $redirectUrl .= '&view=session'; 
        }
        
        $context->response->setRedirect(JRoute::_($redirectUrl));
        $context->response->status = 200;
        
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
        $data = $context->data;
        $person = parent::_actionEdit( $context );
        
        //add the validations here
        $this->getRepository()
        ->getValidator()
        ->addValidation( 'username', 'uniqueness' )
        ->addValidation( 'email', 'uniqueness' );     
                
        if ($person->validate() === false) {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
        }
        
        $person->timestamp();
        
        //manually set the password to make sure there's a password
        if ($data->password) {
            $person->setPassword($data->password);
        }
        
        return $person;      
    }
    
    /**
     * Deletes a person and all of their assets. It also logsout the person.
     * 
     * @param KCommandContext $context Context parameter
     * @return void
     */
    protected function _actionDelete(KCommandContext $context)
    {
        parent::_actionDelete($context);
       
        $this->commit();        
        
        $userId = $this->getItem()->userId;
          
        JFactory::getApplication()->logout($userId);   
        JFactory::getUser($userId)->delete();               
    }
    
    /**
     * Set the necessary redirect
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    public function redirect(KCommandContext $context)
    {
        $url = null;

        if ($context->action == 'delete') {
            $url = 'option=com_people&view=people';
        }

        if ($url) {
            $context->response->setRedirect(JRoute::_($url)); 
        }
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
        $viewer = get_viewer();  
          
        if($viewer->admin()) {
            $subject = 'COM-PEOPLE-MAIL-SUBJECT-ACCOUNT-CREATED';
            $template = 'account_created';    
        } else {
           $subject = 'COM-PEOPLE-MAIL-SUBJECT-ACCOUNT-ACTIVATE';
           $template = 'account_activate'; 
        }
  
        $this->mail(array(
            'to' => $person->email,
            'subject' => sprintf(JText::_($subject), JFactory::getConfig()->getValue('sitename')),
            'template' => $template
        ));   
    }
    
    /**
     * Notify admins that a new admin has joined the network
     *
     * @param KCommandContext $context The context parameter
     *
     * @return void
     */    
    public function mailAdminsNewAdmin(KCommandContext $context)
    {        
        $person = $context->result;
        $this->mailAdmins(array (                          
            'subject' => JText::sprintf('COM-PEOPLE-MAIL-SUBJECT-NEW-ADMIN', $person->name),
            'template' => 'new_admin'
        ));
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
        $viewer = get_viewer();
        
        if ($viewer->admin() || $viewer->eql($this->getItem())) {     
            $tabs->insert('account', array('label' => JText::_('COM-PEOPLE-SETTING-TAB-ACCOUNT')));                    
        } 
    }   
}