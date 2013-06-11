<?php
/**
* @version		$Id: email.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Email filter
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterEmail extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);
		return (false !== filter_var($value, FILTER_VALIDATE_EMAIL));
	}

	/**
	 * Sanitize a value
	 *
	 * Remove all characters except letters, digits and !#$%&'*+-/=?^_`{|}~@.[].
	 *
	 * @param	scalar	Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		$value = trim($value);
		return filter_var($value, FILTER_SANITIZE_EMAIL);
	}
}