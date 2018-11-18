<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
class AnFilterJson extends AnFilterAbstract
{
    /**
     * Constructor
     *
     * @param   object  An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        //Don't walk the incoming data array or object
        $this->_walk = false;
    }

    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return is_string($value) && !is_null(json_decode($value));
    }

    /**
     * Sanitize a value
     *
     * The value passed will be encoded to JSON format.
     *
     * @param   scalar  Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        // If instance of AnConfig casting to string will make it encode itself to JSON
        if ($value instanceof AnConfig) {
            $result = (string) $value;
        } else {
            //Don't re-encode if the value is already in json format
            if (is_string($value) && (json_decode($value) !== null)) {
                $result = $value;
            } else {
                $result = json_encode($value);
            }
        }

        return $result;
    }
}
