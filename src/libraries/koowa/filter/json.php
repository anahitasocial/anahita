<?php
/**
* @version		$Id: json.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Json filter
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterJson extends KFilterAbstract
{
    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
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
        // If instance of KConfig casting to string will make it encode itself to JSON
        if($value instanceof KConfig) {
            $result = (string) $value;
        }
        else
        {
            //Don't re-encode if the value is already in json format
            if(is_string($value) && (json_decode($value) !== NULL)) {
                $result = $value;
            } else {
                $result = json_encode($value);
            }
        }

        return $result;
    }
}