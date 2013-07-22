<?php
/**
* @version		$Id: digit.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Digit filter
 *
 * Checks if all of the characters in the provided string are numerical
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterDigit extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return empty($value) || ctype_digit($value);
    }

    /**
     * Sanitize a value
     *
     * @param   mixed   Value to be sanitized
     * @return  int
     */
    protected function _sanitize($value)
    {
        $value = trim($value);
        $pattern ='/[^0-9]*/';
        return preg_replace($pattern, '', $value);
    }
}

