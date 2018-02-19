<?php

/**
 * Expirable Behavior.
 *
 * It provides a timeframe for an entity with start date and end date
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseDomainBehaviorExpirable extends AnDomainBehaviorAbstract
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
            'attributes' => array(
                'startDate' => array('required' => true, 'type' => 'date', 'default' => 'date'),
                'endDate' => array('required' => true, 'type' => 'date'),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Sets the end date of an expirable.
     *
     * @param AnDomainAttributeDate|AnDate|array $date The end date
     */
    public function setEndDate($date)
    {
        $date = AnDomainAttributeDate::getInstance()->setDate($date);
        $this->set('endDate', $date);

        return $this;
    }

    /**
     * Sets the start date of an expirable.
     *
     * @param AnDomainAttributeDate|AnDate|array $date The start date
     */
    public function setStartDate($date)
    {
        $date = AnDomainAttributeDate::getInstance()->setDate($date);
        $this->set('startDate', $date);

        return $this;
    }
}
