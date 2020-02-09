<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
class AnFilterTerm extends AnFilterAbstract
{
    /**
     * Validate a value.
     *
     * @param   scalar  Value to be validated
     *
     * @return bool True when the variable is valid
     */
    protected function _validate($value)
    {
        $value = trim($value);
        $pattern = '/\pL|[.#@_\-\s0-9]/u';

        return is_string($value) && preg_replace($pattern, '', $value);
    }

    /**
     * Sanitize a value.
     *
     * @param   scalar  Value to be sanitized
     *
     * @return string
     */
    protected function _sanitize($value)
    {
        $value = trim($value);
        $pattern = '/(?![.#@_\-\s0-9])\PL/u';

        return preg_replace($pattern, '', $value);
    }
}
