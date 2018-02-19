<?php

/**
 * Discount Coupon.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainEntityCoupon extends AnDomainEntityDefault
{
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
            'searchable_properties' => array('code'),
            'attributes' => array(
                'id',
                'discount' => array('default' => '0.1'),
                'code' => array(
                    'required' => true,
                    'unique' => true,
                ),
                'limit' => array(
                    'default' => 100,
                    'require' => true,
                ),
                'usage' => array(
                    'default' => 0,
                    'write_access' => 'private',
                ),
                'expiresOn',
            ),
            'behaviors' => array(
                'authorizer',
                'modifiable',
                'locatable',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Initializes the options for an entity after being created.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _afterEntityInstantiate(KConfig $config)
    {
        $config->append(array(
            'data' => array(
                'code' => md5(uniqid('')),
            ),
        ));
    }

    /**
     * Set the discount, if the discount value is adjusted to be within the range
     * of 0 and 100.
     *
     * @param float $discount
     */
    public function setDiscount($discount)
    {
        $discount = (float) $discount;

        if ($discount > 1) {
            $discount = min(max($discount, 0), 100);
            $discount = $discount / 100;
        }

        $this->set('discount', $discount);
    }

    /**
     * Increment coupon usage.
     */
    public function used()
    {
        ++$this->usage;
    }

    /**
     * Check if a coupon is valid to be used. If the coupon has been used more than it's limit
     * or has been expired.
     *
     * @return bool
     */
    public function usable()
    {
        return $this->usage < $this->limit && !$this->expired();
    }

    /**
     * Sets the end date of an expirable.
     *
     * @param AnDomainAttributeDate|AnDate|array $date The expirary date
     */
    public function setExpiresOn($date)
    {
        $date = AnDomainAttributeDate::getInstance()->setDate($date);
        $this->set('expiresOn', $date);
    }

    /**
     * Return whether a subscriptions is expired or not.
     *
     * @return bool
     */
    public function expired()
    {
        if (empty($this->expiresOn)) {
            return false;
        }

        return AnDomainAttributeDate::getInstance()->toDate()->compare($this->expiresOn) > 0;
    }
}
