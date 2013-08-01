<?php 
/**
 * @version     $Id$
 * @category	Com_Subscriptions
 * @package		Model
 * @copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://anahitapolis.com
 */

/**
 * Purchase transaction
 * 
 * @category	Com_Subscriptions
 * @package		Model
 */
class ComSubscriptionsDomainEntityOrder extends AnDomainEntityDefault
{	
    /**
     * The order subscriber
     * 
     * @var ComPeopleDomainEntityPerson
     */	
    protected $_subscriber;
    
    /**
     * The order package
     * 
     * @var ComSubscriptionsDomainEntityPackage
     */
    protected $_package;   
    
    /**
     * The payment method
     *
     * @var ComSubscriptionsDomainPaymentMethodInterface
     */
    protected $_payment_method;
        
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
            'resources' => array('subscriptions_transactions')
        ));
    
        parent::_initialize($config);
    }
    
    
	/**
	 * Initialize a new node
	 * 
	 * @return unknown_type
	 */
	protected function _afterEntityInstantiate(KConfig $config)
	{
		$config->append(array('data'=>array(
		    'orderId'       => substr(uniqid(rand(), true), 0, 10),
			'createdOn'     => AnDomainAttributeDate::getInstance()
		)));
	}
		
	/**
	 * Return the item amount after the discount
	 * 
	 * @return float
	 */
	public function getItemAmountAfterDiscount()
	{
		return $this->itemAmount * (1 - $this->get('discountAmount'));
	}
	
	/**
	 * Calculates and return the total price
	 * 
	 * @return float
	 */
	public function getTotalAmount()
	{
		return $this->getItemAmountAfterDiscount() + $this->getTaxAmount();
	}

	/**
	 * Set the transaction person
	 * 
	 * @param ComPeopleDomainEntityPerson $person
	 * 
	 * @return void
	 */
	public function setSubscriber($subscriber)
	{
	    $this->_subscriber = $subscriber;
	    
	    if ( $subscriber->persisted() ) 
	    {
	        $this->userId  = $this->_subscriber->userId;
	        $this->actorId = $this->_subscriber->id;
	    } else {
	        $this->set('actorId', PHP_INT_MAX);
            $this->set('userId',  PHP_INT_MAX);
	    }
	    $this->getRepository()->getSpace()->setSaveOrder($this, $this->_subscriber);
		return $this;
	}

	/**
	 * Return the subscriber
	 * 
	 * @return ComPeopleDomainEntityPerson $person
	 */
	public function getSubscriber()
	{
	    return $this->_subscriber;
	}
	
	/**
	 * Set the transaction price
	 * 
	 * @param ComSubscriptionsDomainEntityPackage $package
	 * @param boolean $upgraded
	 * 
	 * @return void
	 */
	public function setPackage($package, $upgraded = null)
	{
		$this->currency   		= get_config_value('subscriptions.currency','US');
		$this->itemName	  		= $package->name;
		$this->itemId	  		= $package->id;
		$this->itemAmount 		= $package->price;
		$this->duration			= $package->duration;
		$this->billingPeriod 	= $package->billingPeriod;
		$this->recurring 		= $package->recurring;
		if ( $upgraded ) {
			$this->duration 	= max(0, $package->duration - $upgraded->duration);
			$this->itemAmount	= max(0, $package->price - $upgraded->price);
			$this->upgrade		= true;
		}
		$this->_package = $package;
		return $this;
	}
	
	/**
	 * Return a payload object for this order
	 * 
	 * @return ComSubscriptionsDomainPaymentPayload
	 */
	public function getPayload()
	{
	    $payload = $this->getService('com://site/subscriptions.domain.payment.payload', array(
	            'order_id'        => $this->orderId,
	            'description'     => $this->_package->name,
	            'amount'          => $this->getItemAmountAfterDiscount(),
	            'tax_amount'      => $this->getTaxAmount(),
	            'payment_method'  => $this->getPaymentMethod(),
	            'currency'        => $this->currency
        ));
	    
	    if ( $this->_package->recurring ) {
	        $payload->setRecurring(1, $this->_package->billingPeriod, AnDomainAttributeDate::getInstance()->getDate(DATE_FORMAT_ISO_EXTENDED));
	    }
	    
	    return $payload;
	}
	
	/**
	 * The method of payment
	 *
	 * @param ComSubscriptionsDomainPaymentMethodInterface $method The method of payment
	 *
	 * @return void
	 */
	public function setPaymentMethod(ComSubscriptionsDomainPaymentMethodInterface $method)
	{
	    $this->_payment_method = $method;
	    $this->method = (string) $method;
	    return $this;
	}
	
	/**
	 * Return the payment method
	 *
	 * @return ComSubscriptionsDomainPaymentMethodInterface
	 */
	public function getPaymentMethod()
	{
	    return $this->_payment_method;
	}	
	
	/**
	 * Retunr if an order can be processed
	 * 
	 * @return boolean
	 */
	public function canProcess()
	{
	    if ( $this->persisted() ) {
	        return false;
	    }
	    
	    if ( !$this->_subscriber ) {	        
	        return false;
	    }
	    
	    if ( !$this->validateEntity() ) {
	        return false;
	    }
	    
	    if ( !$this->_subscriber->persisted() && 
	            !$this->_subscriber->validateEntity() ) {
	        
	        return false;
	    }
	    
	    if ( !isset($this->_package) ) {
	        return false;
	    }
	    
	    if ( !isset($this->_payment_method) ) {
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * Set the country
	 * 
	 * @param string $country The country code
	 * 
	 * @return void
	 */
	public function setCountry($country)
	{
	    $this->set('country', $country);
	    $vat = $this->getService('repos://site/subscriptions.vat')
	            ->find(array('country'=>$country));	     
	    if ( $vat ) {
	        $this->setVat($vat);
	    }
	    return $this;	    
	}
	
	/**
	 * Return the order package
	 * 
	 * @return ComSubscriptinosDomainEntityPackage
	 */
	public function getPackage()
	{
	    return $this->_package;
	}
				
	/**
	 * Applies the VAT (value added tax) to the transaction
	 * 
	 * @param ComSubscriptionsDomainEntityVat $vat
	 * @return void
	 */
	public function setVat($vat)
	{
		$taxes = array();
		foreach($vat->getFederalTaxes() as $tax)
			$taxes[] = $tax->value;
		$this->taxAmount =  array_sum(array_values($taxes));
		return $this;		
	}

	/**
	 * Return the tax amount
	 * 
	 * @return float
	 */
	public function getDiscountAmount()
	{
		return $this->itemAmount * $this->get('discountAmount');
	}
		
	/**
	 * Return the tax amount
	 * 
	 * @return float
	 */
	public function getTaxAmount()
	{
		return $this->getItemAmountAfterDiscount() * $this->get('taxAmount');
	}
		
	/**
	 * Applies a coupon for the package, once applied the package price is adjusted accordingly. 
	 * 
	 * @param ComSubscriptionsDomainEntityCoupon|string $coupon
	 * @return void
	 */
	public function setCoupon($coupon)
	{		
		$this->discountAmount = $coupon->discount; 
		$this->couponCode	  = $coupon->code;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AnDomainEntityAbstract::cloneEntity()
	 */
	public function cloneEntity($deep = true)
	{
	    $clone = parent::cloneEntity($deep);
	    $clone->setSubscriber($this->_subscriber);
	    $clone->setPackage($this->_package);
	    $clone->setPaymentMethod($this->_payment_method);
	    return $clone;
	}

	/**
	 * Try to increment the coupong before inserting
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityInsert(KCommandContext $context)
	{		    	    
		if ( !empty($this->couponCode) ) {
			$coupon = $this->getService('repos:subscriptions.coupon')->find(array('code'=>$this->couponCode));
			if ( $coupon )			
				$coupon->used();			
		}
	}
}