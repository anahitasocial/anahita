<?php
/**
* @version		$Id: base64.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Base64 filter
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterBase64 extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        $pattern = '#^[a-zA-Z0-9/+]*={0,2}$#';
        return (is_string($value) && preg_match($pattern, $value) == 1);
    }

	/**
     * Sanitize a value
     *
     * @param   scalar  Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        $value = trim($value);
        $pattern = '#[^a-zA-Z0-9/+=]#';
        return preg_replace($pattern, '', $value);
    }
}