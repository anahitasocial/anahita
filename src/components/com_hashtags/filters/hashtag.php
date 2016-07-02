<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Post filter.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComHashtagsFilterHashtag extends KFilterAbstract
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
        $pattern = ComHashtagsDomainEntityHashtag::PATTERN_HASHTAG;

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
        $value = trim($value);
        $pattern = ComHashtagsDomainEntityHashtag::PATTERN_HASHTAG;

        return preg_replace_callback($pattern, function($matches) { return '';}, $value);
    }
}
