<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Payment
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * The method of a payment
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Payment
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsDomainPaymentMethodCreditcard implements ComSubscriptionsDomainPaymentMethodInterface
{
    /**
     * The creditcard
     * 
     * @var Merchant_Billing_CreditCard
     */
    public $creditcard;
    
    /**
     * The adress associated with the creditcard
     * 
     * @var KObject
     */
    public $address;
    
    /**
     * Constructor.
     *
     * @param Merchant_Billing_CreditCard $creditcard The credit card used
     * @param array The address associated with a creditcard
     *
     * @return void
     */
    public function __construct(Merchant_Billing_CreditCard $creditcard, $address)
    {
        $this->creditcard = $creditcard;
        $this->address    = $address;
    }
    
    /**
     * (non-PHPdoc)
     * @see ComSubscriptionsDomainPaymentMethodInterface::__toString()
     */
    public function __toString()
    {
        return 'Credit Card';
    }
}