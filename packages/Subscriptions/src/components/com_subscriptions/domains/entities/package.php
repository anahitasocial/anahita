<?php

/**
 * Subscription of a person with a package.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainEntityPackage extends ComBaseDomainEntityNode
{
    const BILLING_PERIOD_YEAR = 'Year';
    const BILLING_PERIOD_MONTH = 'Month';
    const BILLING_PERIOD_WEEK = 'Week';
    const BILLING_PERIOD_DAY = 'Day';

    /**
     * Package transaction object.
     *
     * @var ComSubscriptionsDomainEntityTransaction
     */
    protected $_transaction;

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
            'resources' => array('subscriptions_packages'),
            'attributes' => array(
                'name' => array('required' => true),
                'body' => array('format' => 'html'),
                'price' => array(
                    'type' => 'float',
                    'required' => true,
                ),
                'duration' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
                'recurring' => array(
                    'type' => 'integer',
                    'default' => 0,
                ),
                'billingPeriod' => array(
                    'default' => self::BILLING_PERIOD_YEAR,
                    'required' => true,
                ),
            ),
            'relationships' => array(
                //let the cleaner to take care of it
                'subscriptions' => array(
                    'parent_delete' => 'ignore',
                 ),
            ),
            'behaviors' => array(
                'authorizer',
                'orderable',
                'describable',
                'enableable',
                'dictionariable',
                'modifiable',
            ),
        ));

        parent::_initialize($config);
    }

    public function setBillingPeriod($billingPeriod)
    {
        $this->set('billingPeriod', $billingPeriod);

        switch ($this->billingPeriod) {
            case self::BILLING_PERIOD_YEAR;
                $this->duration = AnHelperDate::yearToSeconds();
            break;

            case self::BILLING_PERIOD_MONTH;
                $this->duration = AnHelperDate::monthToSeconds();
            break;

            case self::BILLING_PERIOD_WEEK;
                $this->duration = AnHelperDate::weekToSeconds();
            break;

            case self::BILLING_PERIOD_DAY;
                $this->duration = AnHelperDate::dayToSeconds();
            break;
        }
    }

    /**
     * Return the upgrade discount.
     *
     * @return float
     */
    public function getUpgradePrice()
    {
        return (float) $this->price - ($this->price * $this->getUpgradeDiscount());
    }

    /**
     * Return the upgrade discount.
     *
     * @return float
     */
    public function getUpgradeDiscount()
    {
        if ($this->ordering <= 1) {
            return 0;
        }

        return $this->getValue('upgrade_discount', 0);
    }

    /**
     * Set the upgrade discount to a value between 0 and 1.
     *
     * @param float discount The discount amount
     */
    public function setUpgradeDiscount($discount)
    {
        if ($this->ordering <= 1) {
            $discount = 0;
        }

        $discount = (float) min(1, max(0, $discount));
        $this->setValue('upgrade_discount', $discount);

        return $this;
    }

    /**
     * Authorizers subscripting to a package.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    public function authorizeSubscribePackage(KCommandContext $context)
    {
        $viewer = $context->viewer;

        return !$viewer->hasSubscription(false) || $this->authorize('upgradepackage');
    }

    /**
     * Authorizers upgrading to a package.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    public function authorizeUpgradePackage(KCommandContext $context)
    {
        $viewer = $context->viewer;

        if (!$viewer->hasSubscription()) {
            return false;
        }

        $package = $viewer->subscription->package;

        return !$this->eql($package) &&
        (
            $package->duration < $this->duration ||
            $package->price < $this->price
        );
    }

    /**
     * returns actor ids as an array.
     *
     * @return array of actor ids that subscribers to this package will follow
     */
    public function getActorIds()
    {
        $actorIds = $this->getValue('actorIds');

        if ($actorIds) {
            $actorIds = explode(',', $actorIds);
            $actorIds = array_unique($actorIds);
        } else {
            $actorIds = array();
        }

        return $actorIds;
    }
}
