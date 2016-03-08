<?php

/**
 * Session filter.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleFilterReturn extends KFilterInternalurl
{
    /**
     * Sanitize a value.
     *
     * @param   mixed   Value to be sanitized
     *
     * @return string
     */
    protected function _sanitize($value)
    {
        $value = parent::_sanitize($value);
        $pattern = '/[()<\/>\"\']*/';
        $value = preg_replace_callback($pattern, function($matches) { return '';} , $value);

        return $value;
    }
}
