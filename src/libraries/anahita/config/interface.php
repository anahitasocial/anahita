<?php
/**
 * @package     Anahita_Config
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
interface AnConfigInterface extends IteratorAggregate, ArrayAccess, Countable
{
    /**
     * Retrieve a configuration item and return $default if there is no element set.
     *
     * @param string
     * @param mixed
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Append values
     *
     * This funciton only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  mixed    A value of an or array of values to be appended
     * @return AnConfig
     */
    public function append($config);

    /**
     * Return the data
     *
     * If the data being passed is an instance of AnConfig the data will be transformed
     * to an associative array.
     *
     * @return array|scalar
     */
    public static function unbox($data);

    /**
     * Return an associative array of the config data.
     *
     * @return array
     */
    public function toArray();

    /**
     * Returns a string with the encapsulated data in JSON format
     *
     * @return string   returns the data encoded to JSON
     */
    public function toJson();
}
