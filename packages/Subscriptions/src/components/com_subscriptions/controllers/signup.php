<?php


 /**
  * Signup Controller.
  *
  * @controller		Controller
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

        $this->registerCallback(array(
          'before.process',
          'before.payment',
          'before.confirm',
          'before.xpayment',
          'layout.payment', ),
          array($this, 'validateUser'));

        $this->registerCallback(array(
          'before.process',
          'before.confirm',
          'layout.confirm', ),
          array($this, 'validatePayment'));

        $this->getService('repos:people.person')->getValidator()
             ->addValidation('username', 'uniqueness')
             ->addValidation('email', 'uniqueness');
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => to_hash(array(
                'identifiable' => array('repository' => 'package'),
                'committable',
                'validatable',
            )),
        ));

        parent::_initialize($config);
    }

    /**
     * Renders the sign up view.
     *
     * @param KCommandContext $context
     */
    protected function _actionGet(KCommandContext $context)
    {
        $this->_request->append(array(
            'layout' => 'default',
        ));

        $this->getCommandChain()->run('layout.'.$this->getRequest()->get('layout'), $context);

        if ($this->getRequest()->get('layout') == 'login' && !$this->viewer->guest()) {
            $url = route('option=com_subscriptions&view=signup&layout=payment&id='.$this->getItem()->id);
            $context->response->setRedirect($url);
            return false;
        }

        return parent::_actionGet($context);
    }

    /**
     * Confirm the payment.
     *
     * @param KCommandContext $context
     */
    protected function _actionPayment($context)
    {
        $url = route('option=com_subscriptions&view=signup&layout=payment&id='.$this->getItem()->id);
        $context->response->setRedirect($url);
    }

    /**
     * Confirm the payment.
     *
     * @param KCommandContext $context
     */
    protected function _actionConfirm($context)
    {
        $url = route('option=com_subscriptions&view=signup&layout=confirm&id='.$this->getItem()->id);
        $context->response->setRedirect($url);
    }

    /**
     * Express Payment.
     *
     * @param KCommandContext $context
     */
    protected function _actionXpayment($context)
    {
        $data = $context->data;
        $package = $this->getItem();
        $gateway = $this->getService('com:subscriptions.controller.subscription')->getGateway();

        try {

            $payload = $this->order->getPayload();
            $confirm = route('option=com_subscriptions&view=signup&action=confirm&xpayment=true&id='.$package->id, true);
            $cancel = route('option=com_subscriptions&view=signup&action=cancel&xpayment=true&id='.$package->id, true);
            $url = $gateway->getAuthorizationURL($payload, $confirm, $cancel);
            $context->response->setRedirect($url, KHttpResponse::SEE_OTHER);

        } catch (Exception $error) {
            throw new RuntimeException($error->getMessage());
        }
    }

    /**
     * Process Action.
     *
     * @param KCommandContext $context
     *
     * @return boolean
     */
    protected function _actionProcess($context)
    {
        try {
            $identifier = 'com:subscriptions.controller.subscription';
            $subscription = $this->getService($identifier)->setOrder($this->order->cloneEntity())->add();
        } catch (ComSubscriptionsDomainPaymentException $exception) {
            $this->setMessage('COM-SUBSCRIPTIONS-TRANSACTION-ERROR', 'error');
            throw new RuntimeException('Payment process error');
        }

        if ($subscription->persisted()) {

            KRequest::set('session.signup', null);
            KRequest::set('session.subscriber_id', $subscription->person->id);
            $url = route('option=com_subscriptions&view=signup&layout=processed&id='.$this->getItem()->id);

            if (get_viewer()->guest()) {
                $return = base64UrlEncode($url);
                $token = $subscription->person->activationCode;
                $url = route('option=com_people&view=session&token='.$token.'&return='.$return);
            }

            $context->response->setRedirect($url);

        } else {
            throw new RuntimeException("Couldn't subscribe");
        }

        return true;
    }

    /**
     * Validates before confirm and process.
     *
     * @param KCommandContext $context
     *
     * @return bool
     */
    public function validatePayment(KCommandContext $context)
    {
        $data = $context->data;
        $package = $this->getItem();

        if ($data->token) {

            $country = null;
            $identifier = 'com:subscriptions.controller.subscription';
            $method = $this->getService($identifier)->getGateway()->getExpressPaymentMethod($data->token, $country);
            $this->order->setPaymentMethod($method);
            $this->order->country = $country;

        } else {

            $url = route('option=com_subscriptions&view=signup&layout=payment&id='.$package->id);
            $error = false;

            //validate creditcard
            if (!$this->creditcard->is_valid()) {
                $error = true;
                $this->storeValue('credit_card_error', AnTranslator::_('COM-SUBSCRIPTIONS-CREDITCARD-INVALID'));
            }

            //validate contact
            $contact = $this->contact;

            if (! (
                  $contact->address &&
                  $contact->city &&
                  $contact->country &&
                  $contact->state &&
                  $contact->zip
            )) {
                $this->storeValue('address_error', AnTranslator::_('COM-SUBSCRIPTIONS-BILLING-INVALID'));
            }

            if ($error) {
                $context->response->setRedirect($url);
                return false;
            }

            $this->order->country = $contact->country;

            return true;
        }
    }

    /**
     * Validates a user.
     *
     * @param KCommandContext $context
     *
     * @return bool
     */
    public function validateUser(KCommandContext $context)
    {
        $package = $this->getItem();

        //validate user
        if (get_viewer()->guest() && !$this->person->validateEntity()) {
            $url = route('option=com_subscriptions&view=signup&layout=login&id='.$package->id);
            $context->response->setRedirect($url);
            return false;
        }

        return true;
    }

    /**
     * Fetches an entity.
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
     * Override context.
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
     * Instantiate Data from the session.
     *
     * @param $context
     */
    public function instantiateDataFromSession(KCommandContext $context)
    {
        $data = $context->data;
        $package = $this->getItem();

        //create a dummy transaction
        $viewer = get_viewer();
        $this->order = $this->getService('repos:subscriptions.order')->getEntity()->reset();
        $this->order->setPackage($package, $viewer->hasSubscription() ? $viewer->subscription->package : null);

        $this->_instantiateCoupon($data);
        $this->_instantiateUser($data);
        $this->_instantiateCreditCard($data);
    }

    /**
     * Instantiate coupon.
     *
     * @param KConfig $data The request data
     */
    protected function _instantiateCoupon(KConfig $data)
    {
        $this->coupon_code = $data->coupon_code;
        $this->coupon = $this->getService('repos:subscriptions.coupon')
                             ->find(array('code' => $this->coupon_code));

        if (!$this->coupon) {
            $this->coupon_code = '';
        } else {
            $this->order->setCoupon($this->coupon);
        }
    }

    /**
     * Instantiate a contact object.
     *
     * @param KConfig $data The request data
     *
     * @return KObject
     */
    protected function _instantiateContact($data)
    {
        $data->append(array(
            'contact' => new KConfig(),
        ));

        $contact = new KObject();
        $contact->set(array(
            'address' => $data->contact->address,
            'city' => $data->contact->city,
            'country' => $data->contact->country,
            'state' => $data->contact->state,
            'zip' => $data->contact->zip,
        ));

        $this->contact = $contact;

        return $contact;
    }

    /**
     * Instantiate a Merchant_Billing_CreditCard object.
     *
     * @param KConfig $data
     *
     * @return Merchant_Billing_CreditCard
     */
    protected function _instantiateCreditCard($data)
    {
        if ($data->token) {
            unset($data['creditcard']);
            unset($data['contact']);
            return;
        }

        $data->append(array(
            'creditcard' => new KConfig(),
        ));

        $creditcard = $data->creditcard;
        $name = trim($creditcard->name);
        $space = KHelperString::strpos($name, ' ');

        $creditcard_data = array(
            'type' => $creditcard->type,
            'first_name' => KHelperString::ucwords(KHelperString::substr($name, 0, $space)),
            'last_name' => KHelperString::ucwords(KHelperString::substr($name, $space + 1)),
            'number' => $creditcard->number,
            'month' => $creditcard->month,
            'year' => $creditcard->year,
            'verification_value' => $creditcard->csv,
        );

        $creditcard = new Merchant_Billing_CreditCard($creditcard_data);
        $this->creditcard = $creditcard;
        $contact = $this->_instantiateContact($data);
        $this->order->setPaymentMethod(new ComSubscriptionsDomainPaymentMethodCreditcard($creditcard, $contact));

        return $creditcard;
    }

    /**
     * Instantiate a ComPeopleDomainEntityPerson object
     *
     * @param KConfig $data
     *
     * @return ComPeopleDomainEntityPerson instance
     */
    protected function _instantiateUser($data)
    {
        if (get_viewer()->guest()) {

            $data->append(array(
                'person' => new KConfig(),
            ));

            $person_data = array(
                'givenName' => $data->person->givenName,
                'familyName' => $data->person->familyName,
                'email' => $data->person->email,
                'username' => $data->person->username,
                'password' => $data->person->password,
                'usertype' => ComPeopleDomainEntityPerson::USERTYPE_REGISTERED
            );

            $person = $this->getService('repos:people.person')
                           ->getEntity()
                           ->reset()
                           ->setData($person_data);
        } else {
            $person = get_viewer();
        }

        $this->order->setSubscriber($person);
        $this->person = $person;

        return $person;
    }
 }
