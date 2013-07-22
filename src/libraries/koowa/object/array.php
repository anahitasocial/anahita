<?php
/**
 * @version		$Id: array.php 4639 2012-05-13 16:43:53Z johanjanssens $
 * @package		Koowa_Object
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * An Object Array Class
 *
 * The KObjectArray class provides provides the main functionalities of array and at
 * the same time implement the features of KObject
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Object
 */
class KObjectArray extends KObject implements IteratorAggregate, ArrayAccess, Serializable
{
   /**
     * The data for each key in the array (key => value).
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     *
     * @param KConfig|null $config  An optional KConfig object with configuration options
     * @return \KObjectArray
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();

        parent::__construct($config);

        $this->_data = KConfig::unbox($config->data);
    }

 	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $object An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'data'  => array(),
        ));

        parent::_initialize($config);
    }

 	/**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param   int   $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * Get an item from the array by offset
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset
     * @return  mixed The item from the array
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Set an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset
     * @param   mixed   $value
     * @return  KObjectArray
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->__set($offset, $value);
        }

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
     * @param   int     $offset
     * @return  KObjectArray
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
        return $this;
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
     * Serialize
     *
     * Required by interface Serializable
     *
     * @return  string
     */
    public function serialize()
    {
        return serialize($this->_data);
    }

    /**
     * Unserialize
     *
     * Required by interface Serializable
     *
     * @param   string  $data
     */
    public function unserialize($data)
    {
        $this->_data = unserialize($data);
    }

    /**
     * Get a value by key
     *
     * @param   string  $key The key name.
     * @return  string  The corresponding value.
     */
    public function __get($key)
    {
        $result = null;
        if(isset($this->_data[$key])) {
            $result = $this->_data[$key];
        }

        return $result;
    }

    /**
     * Set a value by key
     *
     * @param   string  $key   The key name
     * @param   mixed   $value The value for the key
     * @return  void
     */
    public function __set($key, $value)
    {
       $this->_data[$key] = $value;
     }

	/**
     * Test existence of a key
     *
     * @param  string  $key The key name
     * @return boolean
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Unset a key
     *
     * @param   string  $key The key name
     * @return  void
     */
    public function __unset($key)
    {
         unset($this->_data[$key]);
    }

 	/**
     * Return an associative array of the data
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }
}