<?php

/**
 * Payload object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainPaymentPayload extends KObject
{
    /**
     * Description.
     *
     * @var string
     */
    public $description;

    /**
     * Amount.
     *
     * @var int
     */
    public $amount;

    /**
     * Tax Amount.
     *
     * @var int
     */
    public $tax_amount;

    /**
     * Order ID.
     *
     * @var string
     */
    public $order_id;

    /**
     * The payment method.
     *
     * @var ComSubscriptionsDomainPaymentMethodInterface
     */
    public $payment_method;

    /**
     * The order currency.
     *
     * @var string
     */
    public $curreny;

    /**
     * Flag to determine if an order is recurring.
     *
     * @var array
     */
    protected $_recurring_options;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Set the recurring options.
     *
     * @param int    $frequency  The frequency
     * @param string $unit       The unit for which the frequency happens
     * @param AnDate  $start_date The start date
     */
    public function setRecurring($frequency, $unit, $start_date)
    {
        $this->_recurring_options = new KConfig(array(
             'frequency' => $frequency, 'unit' => $unit, 'start_date' => $start_date,
        ));

        return $this;
    }

    /**
     * Return the payload total amount.
     *
     * @return int
     */
    public function getTotalAmount()
    {
        return $this->amount + $this->tax_amount;
    }

    /**
     * Return the recurring options if ther are any.
     *
     * @return KConfig
     */
    public function getRecurring()
    {
        return $this->_recurring_options;
    }
}
