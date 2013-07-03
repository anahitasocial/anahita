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
 * Payload object
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Payment
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsDomainPaymentPayload extends KObject
{
    /**
     * Description
     *
     * @var string
     */    
    public $description;
    
    /**
     * Amount
     *
     * @var int
     */
    public $amount;    
    
    /**
     * Tax Amount
     *
     * @var int
     */
    public $tax_amount;
    
    /**
     * Order ID
     * 
     * @var string
     */
    public $order_id;
    
    /**
     * The payment method
     * 
     * @var ComSubscriptionsDomainPaymentMethodInterface
     */
    public $payment_method;
    
    /**
     * The order currency
     * 
     * @var string
     */
    public $curreny;
    
    /**
     * Flag to determine if an order is recurring
     *
     * @var array
     */
    protected $_recurring_options;
    
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
        
        foreach($config as $key => $value) {
            $this->$key = $value;    
        }
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
            
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Set the recurring options
     * 
     * @param int    $frequency  The frequency 
     * @param string $unit       The unit for which the frequency happens 
     * @param KDate  $start_date The start date
     * 
     * @return void
     */
    public function setRecurring($frequency, $unit, $start_date)
    {
        $this->_recurring_options = new KConfig(array(
             'frequency' => $frequency, 'unit' => $unit, 'start_date' => $start_date
        ));
        return $this;
    }
    
    /**
     * Return the payload total amount
     * 
     * @return int
     */
    public function getTotalAmount()
    {
        return $this->amount + $this->tax_amount;
    }
    
    /**
     * Return the recurring options if ther are any
     * 
     * @return KConfig
     */
    public function getRecurring()
    {
        return $this->_recurring_options;
    }      
}