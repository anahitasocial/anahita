<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
class AnFilterInt extends AnFilterAbstract
{
    /**
     * Validate a value
     *
     * @param	scalar	Value to be validated
     * @return	bool	True when the variable is valid
     */
    protected function _validate($value)
    {
        return empty($value) || (false !== filter_var($value, FILTER_VALIDATE_INT));
    }

    /**
     * Sanitize a value
     *
     * @param	scalar	Value to be sanitized
     * @return	int
     */
    protected function _sanitize($value)
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}
