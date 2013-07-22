<?php
/**
 * @version     $Id: date.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Date
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * "YYYY-MM-DD HH:MM:SS"
 */
define('DATE_FORMAT_ISO', 1);
/**
 * "YYYYMMSSTHHMMSS(Z|(+/-)HHMM)?"
 */
define('DATE_FORMAT_ISO_BASIC', 2);
/**
 * "YYYY-MM-SSTHH:MM:SS(Z|(+/-)HH:MM)?"
 */
define('DATE_FORMAT_ISO_EXTENDED', 3);
/**
 * "YYYY-MM-SSTHH:MM:SS(.S*)?(Z|(+/-)HH:MM)?"
 */
define('DATE_FORMAT_ISO_EXTENDED_MICROTIME', 6);
/**
 * "YYYYMMDDHHMMSS"
 */
define('DATE_FORMAT_TIMESTAMP', 4);
/**
 * long int, seconds since the unix epoch
 */
define('DATE_FORMAT_UNIXTIME', 5);

define( 'SECONDS_IN_HOUR', 3600 );
define( 'SECONDS_IN_DAY', 86400 );

 /**
 * Date object
 *
 * This class draws heavily on PEAR:Date Copyright (c) 1997-2005 Baba Buehler,
 * Pierre-Alain Joye Released under the New BSD license
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Date
 * @uses        KObject
 */
class KDate extends KObject
{
    /**
     * The year
     *
     * @var int
     */
    public $year;

    /**
     *  The month
     *
     * @var int
     */
    public $month;

    /**
     * The day
     *
     * @var int
     */
    public $day;

    /**
     * The hour
     *
     * @var int
     */
    public $hour;

    /**
     * The minute
     *
     * @var int
     */
    public $minute;

    /**
     * The second
     *
     * @var int
     */
    public $second;

    /**
     * Part second
     *
     * @var float
     */
    public $partsecond;

    /**
     * Constructor
     *
     * Creates a new Date Object initialized to the current date/time in the
     * system-default timezone by default.  A date optionally
     * passed in may be in the ISO 8601, TIMESTAMP or UNIXTIME format,
     * or another Date object.  If no date is passed, the current date/time
     * is used.
     *
     * @see setDate()
     * @param object    An optional KConfig object with configuration options
                        Recognized key values include 'date'
     * @return KDate The new Date object
     */
    public function __construct( KConfig $config = null)
    { 
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();
        
        parent::__construct($config);

        if ($config->date instanceof KDate) {
            $this->copy( $config->date );
        } else {
            $this->setDate( $config->date );
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'date'  => date( 'Y-m-d H:i:s' )
        ));

         parent::_initialize($config);
    }

    /**
     * Set the fields of a Date object based on the input date and format
     *
     * Set the fields of a Date object based on the input date and format,
     * which is specified by the DATE_FORMAT_* constants.
     *
     * @param   string  $date input date
     * @param   int     $format Optional format constant (DATE_FORMAT_*) of the input date.
     *                  This parameter isn't really needed anymore, but you could
     *                  use it to force DATE_FORMAT_UNIXTIME.
     * @return KDate
     */
    public function setDate( $date, $format = DATE_FORMAT_ISO )
    {
        $regex = '/^(\d{4})-?(\d{2})-?(\d{2})([T\s]?(\d{2}):?(\d{2}):?(\d{2})(\.\d+)?(Z|[\+\-]\d{2}:?\d{2})?)?$/i';

        if (preg_match( $regex, $date, $regs ) && $format != DATE_FORMAT_UNIXTIME)
        {
            $this->year         = $regs[1];
            $this->month        = $regs[2];
            $this->day          = $regs[3];
            $this->hour         = isset( $regs[5] ) ? $regs[5] : 0;
            $this->minute       = isset( $regs[6] ) ? $regs[6] : 0;
            $this->second       = isset( $regs[7] ) ? $regs[7] : 0;
            $this->partsecond   = (float) isset( $regs[8] ) ? $regs[8] : 0;
        }
        elseif (is_numeric( $date ))
        {
            // UNIXTIME
            $this->setDate( date( 'Y-m-d H:i:s', $date ) );
        }
        else
        {
            // unknown format
            $this->year         = 0;
            $this->month        = 1;
            $this->day          = 1;
            $this->hour         = 0;
            $this->minute       = 0;
            $this->second       = 0;
            $this->partsecond   = (float)0;
        }

        return $this;
    }

    /**
     * Get a string (or other) representation of this date
     *
     * Get a string (or other) representation of this date in the
     * format specified by the DATE_FORMAT_* constants.
     *
     * @param int $format format constant (DATE_FORMAT_*) of the output date
     * @return string the date in the requested format
     */
    public function getDate( $format = DATE_FORMAT_ISO )
    {
        switch ($format)
        {
            case DATE_FORMAT_ISO:
                return $this->format( '%Y-%m-%d %H:%M:%S' );
                break;

            case DATE_FORMAT_ISO_BASIC:
                $format = '%Y%m%dT%H%M%S';
                return $this->format($format);
                break;

            case DATE_FORMAT_ISO_EXTENDED:
                $format = '%Y-%m-%dT%H:%M:%S';
                return $this->format($format);
                break;

            case DATE_FORMAT_ISO_EXTENDED_MICROTIME:
                $format = '%Y-%m-%dT%H:%M:%s';
                return $this->format($format);
                break;

            case DATE_FORMAT_TIMESTAMP:
                return $this->format( '%Y%m%d%H%M%S' );
                break;

            case DATE_FORMAT_UNIXTIME:
                return mktime( $this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year );
                break;

            default:
                return $this->format( $format );
                break;
        }
    }

    /**
     * Copy values from another Date object
     *
     * Makes this Date a copy of another Date object.
     *
     * @param object Date $date Date to copy from
     */
    public function copy( $date )
    {
        $this->year = $date->year;
        $this->month = $date->month;
        $this->day = $date->day;
        $this->hour = $date->hour;
        $this->minute = $date->minute;
        $this->second = $date->second;
    }

    /**
     * Formats the date
     */
    public function format( $format )
    {
        $timestamp = mktime( $this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year );
        return strftime( $format, $timestamp );
    }

    public function getTimestamp()
    {
        $timestamp = mktime( $this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year );
        return $timestamp;
    }

    /**
     * Set the year field of the date object
     *
     * @param   int     the year
     * @return  int     the year
     */
    public function year( $value = null )
    {
        if ($value !== null)
        {
            if ($value < 0 || $value > 9999) {
                $this->year = 0;
            } else {
                $this->year = $value;
            }
        }
        return $this->year;
    }

    /**
     * Set the month field of the date object
     *
     * @param   int     the month
     * @return  int     the month
     */
    public function month( $value = null )
    {
        if ($value !== null)
        {
            if ($value < 1 || $value > 12) {
                $this->month = 1;
            } else {
                $this->month = $value;
            }
        }
        return $this->month;
    }

    /**
     * Set the day field of the date object
     *
     * @param int the day
     * @return int the day
     */
    public function day( $value = null )
    {
        if ($value !== null)
        {
            if ($value > 31 || $value < 1) {
                $this->day = 1;
            } else {
                $this->day = $value;
            }
        }
        return $this->day;
    }

    /**
     * Set the hour field of the date object
     *
     * @param int the hour
     * @return int the hour
     */
    public function hour( $value = null )
    {
        if ($value !== null)
        {
            if ($value > 23 || $value < 0) {
                $this->hour = 0;
            } else {
                $this->hour = $value;
            }
        }
        return $this->hour;
    }

    /**
     * Set the minute field of the date object
     *
     * @param int the minute
     * @return int the minute
     */
    public function minute( $value = null )
    {
        if ($value !== null)
        {
            if ($value > 59 || $value < 0) {
                $this->minute = 0;
            } else {
                $this->minute = $value;
            }
        }
        return $this->minute;
    }

    /**
     * Set the second field of the date object
     *
     * @param int the second
     * @return int the second
     */
    public function second( $value = null )
    {
        if ($value !== null)
        {
            if ($value > 59 || $value < 0) {
                $this->second = 0;
            } else {
                $this->second = $value;
            }
        }
        return $this->second;
    }

    /**
     * Adds (+/-) a number of years to the current date.
     *
     * @return KDate
     */
    public function addYears( $n )
    {
        $this->year += $n;
        return $this;
    }

    /**
     * Adds (+/-) a number of months to the current date.
     *
     * @param int Positive or negative number of months
     * @return KDate
     */
    public function addMonths( $n )
    {
        $an = abs( $n );
        $years = floor( $an / 12 );
        $months = $an % 12;

        if ($n < 0)
        {
            $this->year -= $years;
            $this->month -= $months;
            if ($this->month < 1) {
                $this->year--;
                $this->month = 12 + $this->month;
            }
        }
        else
        {
            $this->year += $years;
            $this->month += $months;
            if ($this->month > 12) {
                $this->year++;
                $this->month -= 12;
            }
        }

        return $this;
    }

    /**
     * Adds (+/-) a number of days to the current date.
     *
     * @param int The number of days
     * @return KDate
     */
    public function addDays( $n )
    {
        $this->setDate( $this->getTimestamp() + SECONDS_IN_DAY * $n, DATE_FORMAT_UNIXTIME );
        return $this;
    }

    /**
     * Adds (+/-) a number of hours to the current date.
     *
     * @param int The number of days
     * @return KDate
     */
    public function addHours( $n )
    {
        $this->setDate( $this->getTimestamp() + SECONDS_IN_HOUR * $n, DATE_FORMAT_UNIXTIME );
        return $this;
    }

    /**
     * Adds (+/-) a number of minutes to the current date.
     *
     * @param int The number of days
     * @return KDate
     */
    public function addMinutes( $n )
    {
        $this->setDate( $this->getTimestamp() + 60 * $n, DATE_FORMAT_UNIXTIME );
        return $this;
    }

    /**
     * Adds (+/-) a number of seconds to the current date.
     *
     * @param int The number of days
     * @return KDate
     */
    public function addSeconds( $n )
    {
        $this->setDate( $this->getTimestamp() + $n, DATE_FORMAT_UNIXTIME );
        return $this;
    }

    /**
     * Converts a date to number of days since a distant unspecified epoch
     *
     * @param int    the day of the month
     * @param int    the month
     * @param int    the year.  Use the complete year instead of the abbreviated
     * version. E.g. use 2005, not 05. Do not add leading 0's for years prior to
     * 1000.
     *
     * @return integer  the number of days since the epoch
     */
    public function toDays( KDate $date = null)
    {
        $year   = isset($date) ? $date->year  : $this->year;
        $month  = isset($date) ? $date->month : $this->month;
        $day    = isset($date) ? $date->day   : $this->day;

        $century    = (int) substr( $year, 0, 2 );
        $year       = (int) substr( $year, 2, 2 );

        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century--;
            }
        }

        return (
            floor( (146097 * $century) / 4 ) +
            floor( (1461 * $year) / 4 ) +
            floor( (153 * $month + 2) / 5 ) +
            $day + 1721119);
    }

    /**
     * Returns day of week for given date (0 = Sunday)
     *
     * @param   KDate
     * @return  int     the number of the day in the week
     */
    public function getDayOfWeek( KDate $date = null)
    {
        $year   = isset($date) ? $date->year  : $this->year;
        $month  = isset($date) ? $date->month : $this->month;
        $day    = isset($date) ? $date->day   : $this->day;

        if ($month > 2) {
            $month -= 2;
        } else {
            $month += 10;
            $year--;
        }

        $day = (floor((13 * $month - 1) / 5) +
                $day + ($year % 100) +
                floor(($year % 100) / 4) +
                floor(($year / 100) / 4) - 2 *
                floor($year / 100) + 77);

        $weekday_number = $day - 7 * floor($day / 7);
        return $weekday_number;
    }

    /**
     * Returns the full weekday name for the given date
     *
     * @param   mixed   $day     KDate or the weekday number (0-6)
     * @return  string  the full name of the day of the week
     */
    public static function getWeekdayFullname( $day = null )
    {
        if ($day === null ) {
            $day = new KDate();
        }
        if ($day instanceof KDate ) {
            $weekday    = self::getDayOfWeek( $day );
        } else if (is_int( $day )) {
            $weekday    = $day;
        }
        $names      = self::getWeekDays();
        return $names[$weekday];
    }

    /**
     * Returns the abbreviated weekday name for the given date
     *
     * @param int   $date    the date object
     * @param int   $length  the length of abbreviation
     *
     * @return string  the abbreviated name of the day of the week
     * @see Date_Calc::getWeekdayFullname()
     */
    public static function getWeekdayAbbrname( $day, $length = 3)
    {
        return substr( self::getWeekdayFullname( $day ), 0, $length );
    }

    /**
     * Returns the full month name for the given month
     *
     * @param int   $month   the month
     *
     * @return string  the full name of the month
     */
    public static function getMonthFullname( $month )
    {
        $month  = (int) $month;
        $names  = self::getMonthNames();
        return $names[$month];
    }

    /**
     * Returns the abbreviated month name for the given month
     *
     * @param int   $month   the month
     * @param int   $length  the length of abbreviation
     *
     * @return string  the abbreviated name of the month
     * @see Date_Calc::getMonthFullname
     */
    public static function getMonthAbbrname($month, $length = 3)
    {
        $month = (int) $month;
        return substr(self::getMonthFullname($month), 0, $length);
    }

    /**
     * Returns an array of month names
     *
     * Used to take advantage of the setlocale function to return
     * language specific month names.
     *
     * @returns array  an array of month names
     */
    public static function getMonthNames()
    {
        static $months;
        if(!isset($months))
        {
            $months = array();
            for ($i = 1; $i < 13; $i++) {
                $months[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, 2001));
            }
        }
        return $months;
    }

    /**
     * Returns an array of week days
     *
     * Used to take advantage of the setlocale function to
     * return language specific week days.
     *
     * @returns array  an array of week day names
     */
    public static function getWeekDays()
    {
        static $weekdays = null;
        if ($weekdays == null)
        {
            $weekdays   = array();
            for ($i = 0; $i < 7; $i++) {
                $weekdays[$i] = strftime('%A', mktime(0, 0, 0, 1, $i, 2001));
            }
        }
        return $weekdays;
    }

    /**
     * Find the number of days in the given month
     *
     * @param   int     $month  the month
     * @param   int     $year   the year in four digit format
     * @return  int     the number of days the month has
     */
    public static function getDaysInMonth($month, $year)
    {
        if ($year == 1582 && $month == 10) {
            return 21;  // October 1582 only had 1st-4th and 15th-31st
        }

        if ($month == 2) {
            if (self::isLeapYear($year)) {
                return 29;
             } else {
                return 28;
            }
        } elseif ($month == 4 or $month == 6 or $month == 9 or $month == 11) {
            return 30;
        } else {
            return 31;
        }
    }


    /**
     * Returns true for a leap year, else false
     *
     * @param int   $year   the year.  Use the complete year instead of the
     *                       abbreviated version.  E.g. use 2005, not 05.
     *                       Do not add leading 0's for years prior to 1000.
     * @return boolean
     */
    public static function isLeapYear( $year )
    {
        if (preg_match('/\D/', $year)) {
            return false;
        }
        if ($year < 1000) {
            return false;
        }
        if ($year < 1582) {
            // pre Gregorio XIII - 1582
            return ($year % 4 == 0);
        } else {
            // post Gregorio XIII - 1582
            return (($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0);
        }
    }

    /**
     * @param   KDate
     * @return  boolean
     */
    public static function isToday( KDate $date )
    {
        static $today;
        if (!isset($today)) {
            $today  = new KDate;
        }
        return ($today->day == $date->day && $today->month == $date->month && $today->year == $date->year);
    }
}