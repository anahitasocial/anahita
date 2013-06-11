<?php
/**
* @version		$Id: interface.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Filter interface
 *
 * Validate or sanitize data
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
interface KFilterInterface extends KCommandInterface, KServiceInstantiatable
{
    /**
     * Validate a value or data collection
     *
     * NOTE: This should always be a simple yes/no question (is $value valid?), so
     * only true or false should be returned
     *
     * @param   mixed   Data to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value);

    /**
     * Sanitize a value or data collection
     *
     * @param   mixed   Data to be sanitized
     * @return  mixed
     */
    public function sanitize($value);
}