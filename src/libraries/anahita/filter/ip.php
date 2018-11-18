<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
class AnFilterIP extends AnFilterAbstract
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
        return (false !== filter_var($value, FILTER_VALIDATE_IP));
    }

    /**
     * Sanitize a value
     *
     * @param	scalar	Value to be sanitized
     * @return	string
     */
    protected function _sanitize($value)
    {
        return preg_replace('#[^a-f0-9:\.]#i', '', $value);
    }
}
