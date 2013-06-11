<?php
/**
* @version		$Id: legacy.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link         http://www.nooku.org
*/

/**
 * PHP5.3 compatibility
 */
if(false === function_exists('lcfirst'))
{
    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst( $str )
    {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}

/**
 * APC 3.1.4 compatibility
 */
if(extension_loaded('apc') && !function_exists('apc_exists'))
{
    /**
     * Check if an APC key exists
     *
     * @param  mixed  A string, or an array of strings, that contain keys.
     * @return boolean Returns TRUE if the key exists, otherwise FALSE
     */
    function apc_exists($keys)
    {
		$r;
		apc_fetch($keys,$r);
		return $r;
    }
}