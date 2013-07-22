<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Anahita_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Date Helper
 *
 * @category   Anahita
 * @package    Anahita_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnHelperDate extends KObject 
{	
	/**
	 * Converts the $minute into seconds
	 * 
	 * @param int minute
	 * @return int 
	 */
	static function minuteToSeconds($minute = 1)
	{
		return $minute * 60;
	}
	
	/**
	 * Converts the $hour into seconds
	 * 
	 * @param int hour
	 * @return int 
	 */
	static function hourToSeconds($hour = 1)
	{
		return self::minuteToSeconds($hour * 60);
	}	
			
	/**
	 * Converts the $days into seconds
	 * 
	 * @param int day
	 * @return int 
	 */
	static function dayToSeconds($day = 1)
	{
		return self::hourToSeconds($day * 24);
	}	
	
	/**
	 * Converts the $week into seconds
	 * 
	 * @param int week
	 * @return int 
	 */
	static function weekToSeconds($week = 1)
	{
		return self::dayToSeconds($week * 7);
	}	
	
	/**
	 * Converts the $month into seconds
	 * 
	 * @param int month
	 * @return int 
	 */
	static function monthToSeconds($month = 1)
	{
		return self::dayToSeconds($month * 31);
	}	
	
	/**
	 * Converts the $year into seconds
	 * 
	 * @param int year
	 * @return int 
	 */
	static function yearToSeconds($year = 1)
	{
		return self::monthToSeconds($month * 12);
	}

	/**
	 * Converts Seconds to .. another unit (minute, days and etc)
	 * 
	 * @param string $unit
	 * @param int $seconds
	 * @return int 
	 */
	static function secondsTo($unit, $seconds)
	{
		$method = $unit.'ToSeconds';
		return  (float)$seconds / (float)self::$method();
	}	
}
