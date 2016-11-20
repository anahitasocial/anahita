<?php

/**
 * @category	Com_Subscriptions
 *
 * @copyright   (C) 2008 - 2015 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link        http://www.GetAnahita.com
 */

/**
 * Subscription Controller.
 *
 * @controller		Controller
 */
class ComSubscriptionsControllerSubscription extends ComBaseControllerService
{
    /**
     * The subscription order.
     *
     * @var ComSubscriptionsDomainEntityOrder
     */
    protected $_order;

    /**
     * The gateway.
     *
     * @var ComSubscriptionsDomainPaymentGatewayInterface
     */
    protected $_gateway;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if (!$config->gateway instanceof ComSubscriptionsDomainPaymentGatewayInterface) {
            $config->gateway = $this->getService($config->gateway);
        }

        $this->_gateway = $config->gateway;
        $this->registerCallback('after.add', array($this, 'mailInvoice'));
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'serviceable' => array(
                'except' => array(
                    'browse',
                    'read',
                    'edit'
                )
            ),
            'behaviors' => array(
                'com:mailer.controller.behavior.mailer'
            ),
            'gateway' => 'com:subscriptions.domain.payment.gateway.paypal'
        ));

        parent::_initialize($config);
    }

    /**
     * Adds a subscription.
     *
     * @param KCommandContext $context
     */
    protected function _actionAdd(KCommandContext $context)
    {
        $payload = $this->_order->getPayload();

        if (!$this->_gateway->process($payload)) {
            throw new ComSubscriptionsDomainPaymentException('Payment error. Check the log');
        }

        $this->getService('repos:people.person')
        ->addBehavior('com:subscriptions.domain.behavior.subscriber');
        $person = $this->_order->getSubscriber();
        $package = $this->_order->getPackage();

        if (!$person->persisted()) {
            $person->getRepository()->getSpace()
            ->setEntityState($person, AnDomain::STATE_NEW);
            $person->enable()->saveEntity();

            $this->_order->setSubscriber($person);
        }

        if ($person->hasSubscription() && !$package->recurring) {
            $subscription = $person->changeSubscriptionTo($package);
        } else {
            $subscription = $person->subscribeTo($package);
        }

        if ($payload->getRecurring()) {
            $subscription->setValue('profileId', $payload->getRecurring()->profile_id);
            $subscription->setValue('profileStatus', $payload->getRecurring()->profile_status);
        }

        if (!$this->commit()) {
            throw new RuntimeException("Subscription can not be added");
        }

        $this->getResponse()->status = KHttpResponse::CREATED;
        dispatch_plugin('subscriptions.onAfterSubscribe', array('subscription' => $subscription));
        $this->setItem($subscription);

        return $subscription;
    }

    /**
     * Mail an invoice after adding a subscription.
     *
     * @param KCommandContext $context
     */
    public function mailInvoice(KCommandContext $context)
    {
        if ($this->getItem()) {

            $mails[] = array(
                'to' => $this->getItem()->person->email,
                'subject' => AnTranslator::_('COM-SUBSCRIPTIONS-CONFIRMATION-MESSAGE-SUBJECT'),
                'template' => 'invoice',
            );

            $this->mail($mails);
        }
    }

    /**
     * Return the payment gateway.
     *
     * @return ComSubscriptionsDomainPaymentGatewayInterface
     */
    public function getGateway()
    {
        return $this->_gateway;
    }

    /**
     * Sets the package.
     *
     * @param ComSubscriptionsDomainEntityOrder $order
     */
    public function setOrder($order)
    {
        $this->_order = $order;
        $this->_state->order = $order;

        return $this;
    }

    /**
     * Return the order.
     *
     * @return ComSubscriptionsDomainEntityOrder
     */
    public function getOrder()
    {
        return $this->_order;
    }
}
