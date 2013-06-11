<?php
/**
* @version		$Id: time.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Time filter
 *
 * Validates or sanitizes a value to an ISO-8601 time
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterTime extends KFilterTimestamp
{
    /**
     * Validates that the value is an ISO 8601 time string (hh:ii::ss format).
     *
     * As an alternative, the value may be an array with all of the keys for `H`, `i`, and optionally
     * `s`, in which case the value is converted to an ISO 8601 string before validating it.
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
         // look for His keys?
        if (is_array($value)) {
            $value = $this->_arrayToTime($value);
        }

        $expr = '/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]:[0-5][0-9]$/D';

        return (bool) preg_match($expr, $value) || ($value == '24:00:00');
    }

    /**
     * Forces the value to an ISO-8601 formatted time ("hh:ii:ss").
     *
     * @param string The value to be sanitized.  If an integer, it is used as a Unix timestamp;
     *               otherwise, converted to a Unix timestamp using [[php::strtotime() | ]].
     * @return string The sanitized value
     */
    protected function _sanitize($value)
    {
        // look for His keys?
        if (is_array($value)) {
            $value = $this->_arrayToTime($value);
        }

        $format = 'H:i:s';
        if (is_int($value)) {
            return date($format, $value);
        }

        return date($format, strtotime($value));
    }
}

