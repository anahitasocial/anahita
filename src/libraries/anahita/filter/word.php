<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
class AnFilterWord extends AnFilterAbstract
{
    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        $value = trim($value);
        $pattern = '/^[A-Za-z_]*$/';
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
        $pattern    = '/[^A-Za-z_]*/';
        return preg_replace($pattern, '', $value);
    }
}