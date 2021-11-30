<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
class AnFilterAscii extends AnFilterAbstract
{
    /**
     * Validate a variable
     *
     * Returns true if the string only contains US-ASCII
     *
     * @param	mixed	Variable to be validated
     * @return	bool	True when the variable is valid
     */
    protected function _validate($value)
    {
        return (preg_match('/(?:[^\x00-\x7F])/', $value) !== 1);
    }

    /**
     * Transliterate all unicode characters to US-ASCII. The string must be
     * well-formed UTF8
     *
     * @param	scalar	Variable to be sanitized
     * @return	scalar
     */
    protected function _sanitize($value)
    {
        $string = htmlentities(utf8_decode($value));
        $string = preg_replace(
            array('/&szlig;/','/&(..)lig;/', '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
            array('ss',"$1","$1".'e',"$1"),
            $string
        );

        return $string;
    }
}
