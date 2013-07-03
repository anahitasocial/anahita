<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Discount Coupon
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsDomainEntityCoupon extends AnDomainEntityDefault
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
			'attributes' => array(
				'id' ,
				'discount'	=> array('default'=>'0') ,
				'code'		=> array('required'=>true,'unique'=>true) ,
				'limit'		=> array('default'=>1, 'require'=>true) ,
				'usage'		=> array('default'=>0, 'write_access'=>'private') ,
				'expiresOn' ,
			)
		));
		
		parent::_initialize($config);
		
	}

    /**
     * Initializes the options for an entity after being created
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _afterEntityInstantiate(KConfig $config)
	{
		$config->append(array(
			'data' => array(
				'code' =>  md5(uniqid(''))
			)
		));	
	}
	
	/**
	 * Set the discount, if the discount value is adjusted to be within the range
	 * of 0 and 100
	 * 
	 * @param float $discount
	 * @return void
	 */
	public function setDiscount($discount)
	{
		$discount = (float) $discount;
		
		if ( $discount > 1 )
		{
			$discount = min(max($discount, 0), 100);
			$discount = $discount / 100;
		}	
			
		$this->set('discount', $discount);
	}	
	
	/**
	 * Increment coupon usage
	 * 
	 * @return void
	 */
	public function used()
	{
		$this->usage++;
	}
	
	/**
	 * Check if a coupon is valid to be used. If the coupon has been used more than it's limit
	 * or has been expired
	 * 
	 * @return boolean
	 */
	public function usable()
	{
		return $this->usage < $this->limit && !$this->expired();
	}
	
	/**
	 * Sets the end date of an expirable
	 *
	 * @param  AnDomainAttributeDate|KDate|array $date The expirary date
	 * 
	 * @return void
	 */
	public function setExpiresOn($date)
	{
		$date = AnDomainAttributeDate::getInstance()->setDate($date);
		$this->set('expiresOn', $date);
	}
		
	/**
	 * Return whether a subscriptions is expired or not
	 * 
	 * @return boolean
	 */
	public function expired()
	{			
		if ( empty($this->expiresOn) )					
			return false;
		 
		return AnDomainAttributeDate::getInstance()->toDate()->compare($this->expiresOn) > 0;
	}	
	
}