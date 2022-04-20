<?php

/**
 * Date Attribute.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnDomainAttributeDate extends AnDate implements AnDomainAttributeInterface
{
    /**
     * Factory Method.
     *
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public static function getInstance($config = null)
    {
        static $instance;

        $instance = $instance ? clone $instance : new self();

        if (! $config) {
            $config = new AnConfig();
        }

        $config->append(array(
            'date' => gmdate('Y-m-d H:i:s'),
        ));

        $instance->setDate($config->date);

        return $instance;
    }

    /**
     * Constructor.
     *
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public function __construct($config = null)
    {
        if (! $config) {
            $config = new AnConfig();
        }

        $config->append(array(
            'date' => gmdate('Y-m-d H:i:s'),
        ));

        parent::__construct($config);
    }

    /**
     * Sets the Date's internal date.
     *
     * @param $mixed date
     * @param $format Object[optional]
     */
    public function setDate($date, $format = DATE_FORMAT_ISO)
    {
        if ($date instanceof AnDate) {
            $this->copy($date);
        } elseif (is_array($date) || $date instanceof AnConfig) {
            foreach ($date as $key => $value) {
                $this->$key = $value;
            }
        } else {
            parent::setDate($date, $format);
        }

        return $this;
    }

    /**
     * Return a new date with hour:minute:second to 00:00:00.
     *
     * @param bool $clone If set to true then clone the date and return a new one
     *
     * @return AnDomainAttributeDate
     */
    public function toDate($clone = true)
    {
        $date = $clone ? clone $this : $this;
        $date->hour = 0;
        $date->minute = 0;
        $date->second = 0;
        $date->partsecond = 0;

        return $date;
    }

    /**
     * Compare the reciever with another date. If receiver is the same date as the
     * date it returns 0 if receiver is before the dat it returns 1 and if it's after the date
     * it returns -1.
     *
     * @param AnDate $date
     *
     * @return int
     */
    public function compare(AnDate $date)
    {
        $thisTimestamp = $this->getTimestamp();
        $dateTimestamp = $date->getTimestamp();

        return $thisTimestamp - $dateTimestamp;
    }

    /**
     * Returns a modified date object.
     *
     * @param string $change Modify string
     *
     * @return AnDomainAttributeDate
     */
    public function modify($change)
    {
        $date = clone $this;
        $date->setDate(strtotime($change, $this->getDate(DATE_FORMAT_UNIXTIME)));

        return $date;
    }

    /**
     * Values loaded from the database.
     *
     * @param string $date The date data
     *
     * @return AnDomainAttributeDate
     */
    public function unserialize($date)
    {
        $this->setDate($date);
    }

    /**
     * Return a string date.
     *
     * @return string
     */
    public function serialize()
    {
        return $this->getDate('%Y-%m-%d %H:%M:%S');
    }
}
