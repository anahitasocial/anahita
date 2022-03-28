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
 * @link       http://www.Anahita.io
 */

/**
 * It's the same as {@link AnObjectDecorator} but implements some of the PHP interfaces
 * and forward the calls to the object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnObjectDecorator extends AnObject implements Iterator, ArrayAccess, Countable, Serializable
{
    /**
     * Class methods
     *
     * @var array
     */
    private $__methods = array();

    /**
     * The decorated object
     *
     * @var object
     */
    protected $_object;
    
    /**
     * Constructor
     *
     * @param AnConfig|null $config  An optional AnConfig object with configuration options
     * @return \AnObjectDecorator
     */
    public function __construct(AnConfig $config = null)
    {
        parent::__construct($config);

        $this->_object = $config->object;
    }
    
    /**
     * Get the decorated object
     *
     * @return	object The decorated object
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * Set the decorated object
     *
     * @param 	object $object
     * @return 	\AnObjectDecorator
     */
    public function setObject($object)
    {
        $this->_object = $object;
        return $this;
    }
    
    /**
     * Get a list of all the available methods
     *
     * This function returns an array of all the methods, both native and mixed.
     * It will also return the methods exposed by the decorated object.
     *
     * @return array An array
     */
    public function getMethods()
    {
        if (empty($this->__methods)) {
            $methods = array();
            $object  = $this->getObject();

            if (!($object instanceof AnObject)) {
                $reflection	= new ReflectionClass($object);
                foreach ($reflection->getMethods() as $method) {
                    $methods[] = $method->name;
                }
            } else {
                $methods = $object->getMethods();
            }

            $this->__methods = array_merge(parent::getMethods(), $methods);
        }

        return $this->__methods;
    }
    
    /**
     * Checks if the decorated object or one of it's mixins inherits from a class.
     *
     * @param 	string|object 	$class  The class to check
     * @return 	boolean  Returns TRUE if the object inherits from the class
     */
    public function inherits($class)
    {
        $result = false;
        $object = $this->getObject();

        if ($object instanceof AnObject) {
            $result = $object->inherits($class);
        } else {
            $result = $object instanceof $class;
        }

        return $result;
    }
    
    /**
     * Defined by IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->getObject()->getIterator();
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return \AnObjectArray
     */
    public function rewind()
    {
        $this->getObject()->rewind();

        return $this;
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->getObject()->valid();
    }

    /**
     * Return the key of the current element.
     *
     * @return mixed
     */
    public function key()
    {
        return $this->getObject()->key();
    }

    /**
     * Return the current element.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->getObject()->current();
    }

    /**
     * Move forward to next element.
     */
    public function next()
    {
        return $this->getObject()->next();
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
        return $this->getObject()->count();
    }

    /**
     * Check if the object exists in the queue.
     *
     * Required by interface ArrayAccess
     *
     * @param mixed $offset
     */
    public function offsetExists($offset)
    {
        return $this->getObject()->offsetExists($offset);
    }

    /**
     * Returns the object from the set.
     *
     * Required by interface ArrayAccess
     *
     * @param mixed $offset
     */
    public function offsetGet($offset)
    {
        return $this->getObject()->offsetGet($offset);
    }

    /**
     * Store an object in the set.
     *
     * Required by interface ArrayAccess
     *
     * @param AnObjectHandlable $offset
     * @param mixed            $data
     */
    public function offsetSet($offset, $data)
    {
        $this->getObject()->offsetSet($offset, $data);
    }

    /**
     * Removes an object from the set.
     *
     * Required by interface ArrayAccess
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->getObject()->offsetUnset($offset);
    }

    /**
     * Return a string representation of the set.
     *
     * Required by interface Serializable
     *
     * @return string A serialized object
     */
    public function serialize()
    {
        return $this->getObject()->serialize();
    }

    /**
     * Unserializes a set from its string representation.
     *
     * Required by interface Serializable
     *
     * @param string $serialized The serialized data
     */
    public function unserialize($serialized)
    {
        $this->getObject()->unserialize($serialized);
    }
    
    /**
     * Overloaded set function
     *
     * @param  string $key   The variable name
     * @param  mixed  $value The variable value.
     * @return mixed
     */
    public function __set($key, $value)
    {
        $this->getObject()->$key = $value;
    }

    /**
     * Overloaded get function
     *
     * @param  string $key  The variable name.
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getObject()->$key;
    }

    /**
     * Overloaded isset function
     *
     * Allows testing with empty() and isset() functions
     *
     * @param  string $key The variable name
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->getObject()->$key);
    }

    /**
     * Overloaded isset function
     *
     * Allows unset() on object properties to work
     *
     * @param string $key The variable name.
     * @return void
     */
    public function __unset($key)
    {
        if (isset($this->getObject()->$key)) {
            unset($this->getObject()->$key);
        }
    }

    /**
     * Overloaded call function
     *
     * @param  string 	$method    The function name
     * @param  array  	$arguments The function arguments
     * @throws BadMethodCallException 	If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        $object = $this->getObject();

        //Check if the method exists
        if ($object instanceof AnObject) {
            $methods = $object->getMethods();
            $exists  = in_array($method, $methods);
        } else {
            $exists = method_exists($object, $method);
        }

        //Call the method if it exists
        if ($exists) {
            $result = null;

            // Call_user_func_array is ~3 times slower than direct method calls.
            switch (count($arguments)) {
                case 0:
                    $result = $object->$method();
                    break;
                case 1:
                      $result = $object->$method($arguments[0]);
                       break;
                   case 2:
                       $result = $object->$method($arguments[0], $arguments[1]);
                       break;
                  case 3:
                      $result = $object->$method($arguments[0], $arguments[1], $arguments[2]);
                       break;
                   default:
                     // Resort to using call_user_func_array for many segments
                    $result = call_user_func_array(array($object, $method), $arguments);
             }

            //Allow for method chaining through the decorator
            $class = get_class($object);
            
            if ($result instanceof $class) {
                return $this;
            }

            return $result;
        }

        return parent::__call($method, $arguments);
    }
}
