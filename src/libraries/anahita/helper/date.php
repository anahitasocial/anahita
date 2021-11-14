<?php

/**
 * Date Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       https://www.Anahita.io
 */
class AnHelperDate extends AnObject
{
    /**
     * Scientific values of each time unit.
     */
    const YEAR_IN_SECONDS = 3.15569e7;
    const MONTH_IN_SECONDS = 2.62974e6;

    /**
     * Converts the $minute into seconds.
     *
     * @param int minute
     *
     * @return int
     */
    public static function minuteToSeconds($minute = 1)
    {
        return $minute * 60;
    }

    /**
     * Converts the $hour into seconds.
     *
     * @param int hour
     *
     * @return int
     */
    public static function hourToSeconds($hour = 1)
    {
        return self::minuteToSeconds($hour * 60);
    }

    /**
     * Converts the $days into seconds.
     *
     * @param int day
     *
     * @return int
     */
    public static function dayToSeconds($day = 1)
    {
        return self::hourToSeconds($day * 24);
    }

    /**
     * Converts the $week into seconds.
     *
     * @param int week
     *
     * @return int
     */
    public static function weekToSeconds($week = 1)
    {
        return self::dayToSeconds($week * 7);
    }

    /**
     * Converts the $month into seconds.
     *
     * @param int month
     *
     * @return int
     */
    public static function monthToSeconds($month = 1)
    {
        return self::MONTH_IN_SECONDS * $month;
    }

    /**
     * Converts the $year into seconds.
     *
     * @param int year
     *
     * @return int
     */
    public static function yearToSeconds($year = 1)
    {
        return self::YEAR_IN_SECONDS * $year;
    }

    /**
     * Converts Seconds to .. another unit (minute, days and etc).
     *
     * @param string $unit
     * @param int    $seconds
     *
     * @return int
     */
    public static function secondsTo($unit, $seconds)
    {
        $method = $unit.'ToSeconds';

        return  (float) $seconds / (float) self::$method();
    }
}
