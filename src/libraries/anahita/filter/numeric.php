<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
class AnFilterNumeric extends AnFilterAbstract
{
    /**
     * Validate whether the given variable is numeric. Numeric strings consist of optional sign, any
     * number of digits, optional decimal part and optional exponential part. Thus +0123.45e6 is a
     * valid numeric value. Hexadecimal notation (0xFF) is allowed too but only without sign, decimal
     * and exponential part
     *
     * @param	scalar	Value to be validated
     * @return	bool	True when the variable is valid
     */
    protected function _validate($value)
    {
        return (is_string($value) && is_numeric($value));
    }

    /**
     * Sanitize non-numeric characters from the value.
     *
     * @param	scalar	Value to be sanitized
     * @return	float
     */
    protected function _sanitize($value)
    {
        return (string) filter_var(
            $value,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND | FILTER_FLAG_ALLOW_SCIENTIFIC
        );
    }
}
