<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * It's the same as {@link AnObjectArray} but it allows to use {@link AnObjectHandlable} as
 * keys.
 *
 * <code>
 * $array  = new AnObjectArray();
 * $object = new AnObject();
 * $array[$object] = 'Some Value';
 * </code>
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnObjectArray extends AnObject implements IteratorAggregate, ArrayAccess, Serializable
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
     * @param AnConfig|null $config  An optional AnConfig object with configuration options
     * @return \AnObjectArray
     */
    public function __construct(AnConfig $config = null)
    {
        //If no config is passed create it
        if (!isset($config)) {
            $config = new AnConfig();
        }

        parent::__construct($config);

        $this->_data = AnConfig::unbox($config->data);
    }
     
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   AnConfig $object An optional AnConfig object with configuration options
     * @return  void
     */
    protected function _initialize(AnConfig $config)
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
     * @return  AnObjectArray
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
     * @return  AnObjectArray
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
     * Get a value by key.
     *
     * @param   string  The key name.
     *
     * @return string The corresponding value.
     */
    public function __get($key)
    {
        /*
        $result = null;
        if(isset($this->_data[$key])) {
            $result = $this->_data[$key];
        }

        return $result;
        */
        
        $result = null;
        $key = $this->__key($key);
        if (isset($this->_data[$key])) {
            $result = $this->_data[$key];
        }

        return $result;
    }

    /**
     * Set a value by key.
     *
     * @param   string  The key name.
     * @param   mixed   The value for the key
     */
    public function __set($key, $value)
    {
        // $this->_data[$key] = $value;
        $this->_data[ $this->__key($key) ] = $value;
    }

    /**
     * Test existence of a key.
     *
     * @param  string  The key name.
     *
     * @return bool
     */
    public function __isset($key)
    {
        // return array_key_exists($key, $this->_data);
        return array_key_exists($this->__key($key), $this->_data);
    }

    /**
     * Unset a key.
     *
     * @param   string  The key name.
     */
    public function __unset($key)
    {
        // unset($this->_data[$key]);
        unset($this->_data[ $this->__key($key) ]);
    }

    /**
     * Return a key.
     *
     * @param mixed $key
     *
     * @return string
     */
    private function __key($key)
    {
        if ($key instanceof AnObjectHandlable) {
            $key = $key->getHandle();
        } elseif (gettype($key) == 'object') {
            $key = spl_object_hash($key);
        }

        return $key;
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
