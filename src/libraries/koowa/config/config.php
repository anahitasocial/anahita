<?php
/**
 * @version		$Id: config.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Config
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Config Class
 *
 * KConfig provides a property based interface to an array
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Config
 */
class KConfig implements KConfigInterface
{
    /**
     * The data container
     *
     * @var array
     */
    protected $_data;

    /**
     * Constructor.
     *
     * @param   array|KConfig An associative array of configuration settings or a KConfig instance.
     */
    public function __construct( $config = array() )
    {
        if ($config instanceof KConfig) {
            $data = $config->toArray();
        } else {
            $data = $config;
        }

        $this->_data = array();
        if (is_array($data))
        {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /**
     * Retrieve a configuration item and return $default if there is no element set.
     *
     * @param string
     * @param mixed
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if(isset($this->_data[$name])) {
            $result = $this->_data[$name];
        }

        return $result;
    }

	/**
     * Return the data
     *
     * If the data being passed is an instance of KConfig the data will be transformed
     * to an associative array.
     *
     * @return array|scalar
     */
    public static function unbox($data)
    {
        return ($data instanceof KConfig) ? $data->toArray() : $data;
    }

    /**
     * Append values
     *
     * This funciton only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  mixed    A value of an or array of values to be appended
     * @return KConfig
     */
    public function append($config)
    {
        $config = KConfig::unbox($config);

        if(is_array($config))
        {
            if(!is_numeric(key($config)))
            {
                foreach($config as $key => $value)
                {
                    if(array_key_exists($key, $this->_data))
                    {
                        if(!empty($value) && ($this->_data[$key] instanceof KConfig)) {
                            $this->_data[$key] = $this->_data[$key]->append($value);
                        }
                    }
                    else $this->__set($key, $value);
                }
            }
            else
            {
                foreach($config as $value)
                {
                    if (!in_array($value, $this->_data, true)) {
                        $this->_data[] = $value;
                    }
                 }
            }
        }

        return $this;
    }

    /**
     * Retrieve a configuration element
     *
     * @param string
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Set a configuration element
     *
     * @param  string
     * @param  mixed
     * @return void
     */
    public function __set($name, $value)
    {
        if (is_array($value)) {
            $this->_data[$name] = new self($value);
        } else {
            $this->_data[$name] = $value;
        }
    }

    /**
     * Test existence of a configuration element
     *
     * @param string
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Unset a configuration element
     *
     * @param  string
     * @return void
     */
    public function __unset($name)
    {
        unset($this->_data[$name]);
    }

    /**
     * Get a new iterator
     *
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_data);
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Required by the Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * Get an item from the array by offset
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset
     * @return  mixed   The item from the array
     */
    public function offsetGet($offset)
    {
        $result = null;
        if(isset($this->_data[$offset]))
        {
            $result = $this->_data[$offset];
            if($result instanceof KConfig) {
                $result = $result->toArray();
            }
        }

        return $result;
    }

    /**
     * Set an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset of the item
     * @param   mixed   The item's value
     * @return  object  KConfig
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
        return $this;
    }

    /**
     * Unset an item in the array
     *
     * All numerical array keys will be modified to start counting from zero while
     * literal keys won't be touched.
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset of the item
     * @return  object  KConfig
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
        return $this;
    }

    /**
     * Return an associative array of the config data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $data  = $this->_data;
        foreach ($data as $key => $value)
        {
            if ($value instanceof KConfig) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

 	/**
     * Returns a string with the encapsulated data in JSON format
     *
     * @return string  Returns the data encoded to JSON
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

 	/**
     * Deep clone of this instance to ensure that nested KConfigs
     * are also cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $array = array();
        foreach ($this->_data as $key => $value)
        {
            if ($value instanceof KConfig || $value instanceof stdClass) {
                $array[$key] = clone $value;
            } else {
                $array[$key] = $value;
            }
        }

        $this->_data = $array;
    }

    /**
     * Returns a string with the encapsulated data in JSON format
     *
     * @return string   returns the data encoded to JSON
     */
    public function __toString()
    {
        return $this->toJson();
    }
}