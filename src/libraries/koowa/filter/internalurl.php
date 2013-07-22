<?php
/**
* @version		$Id: internalurl.php 4688 2012-06-04 19:57:05Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Internal url filter
 *
 * Check if an refers to a legal URL inside the system. Use when
 * redirecting to an URL that was passed in a request
 *
 * @todo        Do a proper implementation, see KoowaFilterEditlink for ideas
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterInternalurl extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        if(!is_string($value)) {
            return false;
        }

        if(stripos($value, (string)  dirname(KRequest::url()->getUrl(KHttpUrl::BASE))) !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Sanitize a value
     *
     * @param   scalar  Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        //TODO : internal url's should not only have path and query information
        return filter_var($value, FILTER_SANITIZE_URL);
    }
}

