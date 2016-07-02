<?php

/**
 * Username filter. It's the same as commadn witout sanitizing that way
 * we can generate DomainError.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleFilterUsername extends KFilterAbstract
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
        $pattern = '/^[A-Za-z][A-Za-z0-9_-]*$/';

        return (is_string($value) && (preg_match($pattern, $value)) == 1);
    }

    /**
     * Sanitize a value.
     *
     * @param   mixed   Value to be sanitized
     *
     * @return string
     */
    protected function _sanitize($value)
    {
        //don't allow to sanitize that way we can return an error
        return $value;
    }
}
