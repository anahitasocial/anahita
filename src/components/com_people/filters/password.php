<?php

/**
 * Password filter.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleFilterPassword extends KFilterRaw
{
    /**
     * Password min length.
     */
    public static $MIN_LENGTH = 6;

    /**
     * Sanitize a value.
     *
     * @param   mixed   Value to be sanitized
     *
     * @return string
     */
    protected function _validate($value)
    {
        $ret = parent::_validate($value);

        if ($ret) {
            if (strlen($value) < self::$MIN_LENGTH) {
                $ret = false;
            }
        }

        return $ret;
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
        return $this->_encrypt($value);
    }

    /**
     * Encrypt the password value
     *
     * @param   mixed   Value to be sanitized
     *
     * @return string
     */
    private function _encrypt($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}
