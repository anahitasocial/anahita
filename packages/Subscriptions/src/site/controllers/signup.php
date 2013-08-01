<?php 
/**
 * @version     $Id$
 * @category	Com_Subscriptions
 * @package		Controller
 * @copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://anahitapolis.com
 */

/**
 * Signup Controller
 * 
 * @category	Com_Subscriptions
 * @package		Controller
 */
 class ComSubscriptionsControllerSignup extends ComBaseControllerResource
 {
 	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
				
		$this->registerCallback(array('before.process','before.payment','before.confirm','before.xpayment', 'layout.payment'), array($this, 'validateUser'));		
		$this->registerCallback(array('before.process','before.confirm', 'layout.confirm'), array($this, 'validatePayment'));

		$this->getService('repos://site/people.person')
		    ->getValidator()
		    ->addValidation('username','uniqueness')
		    ->addValidation('email',   'uniqueness')
		;
	}
	
     /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(			
			'behaviors' => to_hash(array(
				'identifiable' => array('repository'=>'package'),
				'committable',
				'validatable'
			))
		));
	
		parent::_initialize($config);
	}  
	
	/**
	 * Renders the sign up view
	 * 
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _actionGet($context)
	{
		$this->_request->append(array(
			'layout' => 'default'
		));

		$this->getCommandChain()->run('layout.'.$this->getRequest()->get('layout'), $context);
		
		if ( $this->getRequest()->get('layout') == 'default' ) 
        {
			$article_id = get_config_value('subscriptions.tos_article_id');
			$tos =& JTable::getInstance('content');
			$tos->load($article_id);
			$this->tos = $tos;				
		} 
		elseif ( $this->getRequest()->get('layout') == 'login' && !$this->viewer->guest() ) 
		{
			$context->response->setRedirect(JRoute::_('option=com_subscriptions&view=signup&layout=payment&id='.$this->getItem()->id));
			return false;
		}

		return parent::_actionGet($context);
	}		
    	
 	/**
	 * Confirm the payment 
	 *
	 * @param KCommandContext $context
	 */
	protected function _actionConfirm($context)
	{
	    $url = JRoute::_('option=com_subscriptions&view=signup&layout=confirm&id='.$this->getItem()->id);	    
		$context->response->setRedirect($url);
	}
	
  	/**
	 * Confirm the payment 
	 *
	 * @param KCommandContext $context
	 */
	protected function _actionPayment($context)
	{
		$url = JRoute::_('option=com_subscriptions&view=signup&layout=payment&id='.$this->getItem()->id);
		$context->response->setRedirect($url);
	}

	/**
	 * Express Payment
	 *
     * @param  KCommandContext $context
     * @return void
     */
	protected function _actionXpayment($context)
	{
		$data 	  	  = $context->data;
		$package  	  = $this->getItem();

		$gateway = $this->getService('com://site/subscriptions.controller.subscription')
		            ->getGateway();
		try 
		{
		    $url = $gateway->getAuthorizationURL($this->order->getPayload(),
		        JRoute::_('option=com_subscriptions&view=signup&action=confirm&xpayment=true&id='.$package->id, true),
		        JRoute::_('option=com_subscriptions&view=signup&action=cancel&xpayment=true&id='.$package->id, true)
		        );
		    $context->response->setRedirect($url, KHttpResponse::SEE_OTHER);
		} 
		
		catch(Exception $e) {
		    throw new RuntimeException();
		}
	}
	
	/**
	 * Logins the user
	 * 
	 * @param KCommandContext $context
	 * 
	 * @return void
	 */
	protected function _actionLogin()
	{
	    $this->getService('com://site/people.controller.person',
	            array('response'=>$this->getResponse()))
	        ->setItem($this->person)
	        ->login();
	}
	
	/**
     * Process Action
     *
     * @param  KCommandContext $context
     * @return void
     */
    protected function _actionProcess($context)
    {      
        try {
            $ret = $this->getService('com://site/subscriptions.controller.subscription')
                ->setOrder($this->order->cloneEntity())
                ->add();
        } catch(ComSubscriptionsDomainPaymentException $exception) 
        {
            $this->setMessage('COM-SUB-TRANSACTION-ERROR', 'error');
            throw new RuntimeException('Payment process error');
        }
        
        if ( $ret ) 
        {
           //clreat the sesion
           $_SESSION['signup'] = null;
           KRequest::set('session.subscriber_id', $ret->person->id);
           $context->response->setRedirect(JRoute::_('option=com_subscriptions&view=signup&layout=processed&id='.$this->getItem()->id));           
        } else {
            throw new RuntimeException('Couldn\'t subscribe');
        }
    }
       
    
    /**
     * Validates before confirm and process
     *
     * @param  KCommandContext $context
     * @return boolean
     */
    public function validatePayment(KCommandContext $context)
    {
    	$data 		= $context->data;
    	$package	= $this->getItem();
		    	
    	if ( $data->token ) 
    	{
    	    $country = null;
    	    
    	    $method = $this->getService('com://site/subscriptions.controller.subscription')
    	            ->getGateway()
    	            ->getExpressPaymentMethod($data->token, $country);
    	        	    			
			$this->order->setPaymentMethod($method);
			$this->order->country = $country;
			
    	} 
    	else 
    	{    	   
            $url = JRoute::_('option=com_subscriptions&view=signup&layout=payment&id='.$package->id);
            
    	    $error = false;
    	    
    	    //validate creditcard
	    	if ( !$this->creditcard->is_valid() ) 
	    	{
	    	    $error = true;
	    	    $this->storeValue('credit_card_error', JText::_('COM-SUB-CREDITCARD-INVALID'));
	    	}
	    	
	    	//validate contact    	
	    	$contact = $this->contact;    	
			if( !($contact->address && $contact->city && $contact->country && $contact->state && $contact->zip) ) 
			{
			    $this->storeValue('address_error', JText::_('COM-SUB-BILLING-INVALID'));                
			}
			
			if ( $error ) {
			    $context->response->setRedirect($url);
			}
			
    		$this->order->country = $contact->country;
    	}
    	    	
    }
     
    /**
     * Validates a user
     *
     * @param  KCommandContext $context
     * @return boolean
     */    
    public function validateUser(KCommandContext $context)
    {
    	$package	= $this->getItem();
        
        //validate user
    	if ( get_viewer()->guest() )
    	{
    	    if ( !$this->person->validateEntity() ) {
    	        $context->response->setRedirect(JRoute::_('option=com_subscriptions&view=signup&layout=login&id='.$package->id));
    	    }
    	}    	
    }
    
	 /**
     * Fetches an entity
     *
     * @param KCommandContext $context
     */
    public function fetchEntity(KCommandContext $context)
    {
    	$entity = $this->getBehavior('identifiable')->fetchEntity($context);
    	$this->instantiateDataFromSession($context);
    	return $entity;
    }
    
    /**
     * Override context     
     */
    public function execute($name, KCommandContext $context)
    {
        $data = $context->data;
        $data->append(KRequest::get('session.signup', 'raw', array()));
        KRequest::set('session.signup', $data->toArray());
        $result = parent::execute($name, $context);
        return $result;
    }

    /**
     * Instantiate Data from the session
     *
     * @param $context
     */
    public function instantiateDataFromSession(KCommandContext $context)
    {
    	$data 		= $context->data;
    	$package 	= $this->getItem();
    	
    	//create a dummy transaction
		$this->order = $this->getService('repos://site/subscriptions.order')
				        ->getEntity()
		                ->reset();
		
		$viewer = get_viewer();
		
		$this->order->setPackage($package, $viewer->hasSubscription() ? $viewer->subscription->package : null);
		        
		$this->_instantiateCoupon($data);		
		$this->_instantiateUser($data);
		$this->_instantiateCreditCard($data);		    
    }
        
    /**
     * Instantiate coupon
     *
     * @param  KConfig $data The request data
     *
     * @return void
     */
    protected function _instantiateCoupon(KConfig $data)
    {
        $this->coupon_code = $data->coupon_code;
        $this->coupon = $this->getService('repos://site/subscriptions.coupon')
            ->find(array('code'=>$this->coupon_code));
        
        if ( !$this->coupon )
            $this->coupon_code = '';
        else
            $this->order->setCoupon($this->coupon);
    }
        
    /**
     * Instantiate a contact object
     *
     * @param  KConfig $data The request data
     * 
     * @return KObject
     */
    protected function _instantiateContact($data)
    { 
		$data->append(array(
    		'contact' => new KConfig()
    	));
    				
		$contact = new KObject();
		$contact->set(array(
			'address' => $data->contact->address,
			'city'	  => $data->contact->city,
			'country' => $data->contact->country,
			'state'	  => $data->contact->state,
			'zip'	  => $data->contact->zip
		));
		
		$this->contact = $contact;
		return $contact;
    }   
     
    /**
     * Instantiate a Merchant_Billing_CreditCard object
     *
     * @param  KConfig $data
     * @return Merchant_Billing_CreditCard
     */
    protected function _instantiateCreditCard($data)
    {    	
        if ( $data->token ) 
        {
            unset($data['creditcard']);
            unset($data['contact']);
            return;    
        }
        
    	$data->append(array(
    		'creditcard' => new KConfig()
    	));
    	
    	$creditcard = $data->creditcard;
    	$name       = trim($creditcard->name);
       	$space      = KHelperString::strpos($name, ' ');
       	
    	$creditcard_data = array(
			'type'		 => $creditcard->type,
	        "first_name" => KHelperString::ucwords(KHelperString::substr($name , 0, $space)),
			"last_name"	 => KHelperString::ucwords(KHelperString::substr($name , $space + 1)),    
	        "number"	 => $creditcard->number,
	        "month" 	 => $creditcard->month,
	        "year" 		 => $creditcard->year,
	        "verification_value" => $creditcard->csv
        );    	
    	$creditcard = new Merchant_Billing_CreditCard($creditcard_data);
        $this->creditcard = $creditcard;
        $contact = $this->_instantiateContact($data);
        $this->order->setPaymentMethod(new ComSubscriptionsDomainPaymentMethodCreditcard($creditcard, $contact));
        return $creditcard;
    }
    
    /**
     * Instantiate a JUser object
     *
     * @param  KConfig $data
     * @return JUser
     */
    protected function _instantiateUser($data)
    {
        if ( get_viewer()->guest() ) 
        {
            $data->append(array(
                    'user' => new KConfig()
            ));
            
            $user_data = array(
                    'userId'    => PHP_INT_MAX,
                    'email' 	=> $data->user->email,
                    'username'  => $data->user->username,
                    'password'	=> $data->user->password,
                    'name'		=> $data->user->name
            );
            
            $person = $this->getService('repos://site/people.person')
            ->getEntity()
            ->reset()
            ->setData($user_data);            
        }    	
        else {
            $person = get_viewer();    
        }
        
		$this->order->setSubscriber($person);
		
		$this->person = $person;
		$this->user   = $person;
		return $person;
    }
 }