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
 * Token Controller. Performs password RESTful operation for reseting a token
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleControllerToken extends ComBaseControllerResource
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
    
        $this->registerCallback('after.add', array($this, 'mailConfirmation'));
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
                'behaviors' => array('com://site/mailer.controller.behavior.mailer')
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Dispatches a correct action based on the state
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _actionPost(KCommandContext $context)
    {
        $result = $this->execute('add',  $context);
        return $result;
    }        
    
    /**
     * Resets a password
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _actionAdd(KCommandContext $context)
    {
        $data  = $context->data;
        $email = $data->email;
        //an email
        $user  = $this->getService('repos://site/users.user')
                    ->getQuery()
                    ->email($email)
                    ->where('IF(@col(block),@col(activation) <> \'\',1)')
                    ->fetch();
        
        if ( $user ) 
        {            
            $this->setMessage('COM-PEOPLE-TOKEN-SENT','success', false);            
            $user->requiresActivation()->save();
            $this->getResponse()->status = KHttpResponse::CREATED;
            $this->user = $user;
        }
        else {
            $this->setMessage('COM-PEOPLE-TOKEN-INVALID-EMAIL','error', false);
            throw new LibBaseControllerExceptionNotFound('Email Not Found');            
        }
    }
    
    /**
     * Send an email confirmation after reset
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    public function mailConfirmation(KCommandContext $context)
    {
        if ( $this->user )
        {
            $this->mail(array(
                    'to' 	   => $this->user->email,
                    'subject'  => sprintf(JText::_('COM-PEOPLE-PASSWORD-RESET-SUBJECT'), JFactory::getConfig()->getValue('sitename')),
                    'template' => $this->user->block ? 'account_activate' : 'password_reset'
            ));
        }
    }    
}