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
        
        $this->getRepository()
        ->getValidator()
        ->addValidation('username','uniqueness')
        ->addValidation('email', 'uniqueness');
        
        if ($person->validate() === false) 
        {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
        }
        
        if (! $person->enabled) 
        {    
            $this->registerCallback( 'after.add', array($this, 'mailActivationLink'));
            $context->response->setHeader('X-User-Activation-Required', true);
            $this->setMessage(JText::sprintf('COM-PEOPLE-ACTIVATION-LINK-SENT', $person->name), 'success');
            $context->response->setRedirect(JRoute::_('option=com_people&view=session'));
        }
        elseif (
            $viewer->guest() && 
            $this->isDispatched() && 
            $context->request->getFormat() == 'html'
        ) 
        {
            $context->response->status = 200;
            $this->registerCallback( 'after.add', array($this, 'login'));
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
        $data = $context->data;
        $person = parent::_actionEdit( $context );
    
        //add the validations here
        $this->getRepository()
        ->getValidator()
        ->addValidation( 'username', 'uniqueness' )
        ->addValidation( 'email', 'uniqueness' );     
              
        //manually set the password to make sure there's a password
        if (! empty($data->password)) 
        {
            $person->setPassword($data->password);
        }
                
        if ($person->validate() === false) 
        {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
        }
        
        return $person;      
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
          
        JFactory::getApplication()->logout($userId);   
        JFactory::getUser($userId)->delete();               
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
            'to' => $this->user->email,
            'subject' => JText::_('COM-PEOPLE-ACTIVATION-SUBJECT'),
            'template' => 'account_activate'
        ));   
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
        $this->mailAdmins(array (                          
            'subject' => JText::sprintf('COM-PEOPLE-NEW-USER-NOTIFICATION-SUBJECT', $this->user->name),
            'template' => 'new_user'
        ));
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
        $user = (array) JFactory::getUser( $this->getItem()->userId );
        $this->getService()->set('com:people.viewer', $this->getItem());
        $controller = $this->getService('com://site/people.controller.session', array('response' => $this->getResponse()));
        
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
        $viewer = get_viewer();
        
        if ($viewer->admin() || $viewer->eql($this->getItem())) 
        {     
            $tabs->insert('account', array('label' => JText::_('COM-PEOPLE-SETTING-TAB-ACCOUNT')));                    
        } 
    }   
}