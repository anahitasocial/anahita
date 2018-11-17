<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
class AnFilterInternalurl extends AnFilterAbstract
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

        if(stripos($value, (string)  dirname(AnRequest::url()->getUrl(AnHttpUrl::SCHEME | AnHttpUrl::HOST))) !== 0) {
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

