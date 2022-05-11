
<?php

/**
 * It's the same as {@link AnObjectSet} but on __get, __set and __call method calls
 * it iterates through its members to perform the same calls.
 *
 * For example
 *
 * <code>
 * $object1 = new AnObject();
 * $object->name = 'This is object 1';
 * $object2 = new AnObject();
 * $object2->name = 'This is object 2';
 *
 * $set = new AnObjectSet();
 * $set->insert($object1);
 * $set->insert($object2);
 *
 * $set->name; //return an array of ['This is object 1','This is object 2'];
 * </code>
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnObjectSet extends AnObject implements Iterator, ArrayAccess, Countable, Serializable
{
    /**
      * Object set
      *
      * @var array
      */
    protected $_object_set = null;
    
    /**
     * Constructor.
     *
     * @param AnConfig|null $config An optional AnConfig object with configuration options
     *
     * @return \AnObjectSet
     */
    public function __construct(AnConfig $config = null)
    {
        //If no config is passed create it
        if (! isset($config)) {
            $config = new AnConfig();
        }

        parent::__construct($config);
        
        $this->_object_set = array();

        if ($config->data) {
            foreach ($config->data as $object) {
                $this->insert($object);
            }
        }
    }
    
    /**
     * Inserts an object in the set
     *
     * @param   AnObjectHandlable $object
     * @return  boolean	TRUE on success FALSE on failure
     */
    public function insert(AnObjectHandlable $object)
    {
        if ($handle = $object->getHandle()) {
            $this->_object_set[$handle] = $object;
            return true;
        }

        return false;
    }
    
    /**
     * Removes an object from the set
     *
     * All numerical array keys will be modified to start counting from zero while
     * literal keys won't be touched.
     *
     * @param   AnObjectHandlable $object
     * @return  AnObjectQueue
     */
    public function extract(AnObjectHandlable $object)
    {
        $handle = $object->getHandle();
        
        if (isset($this->_object_set[$handle])) {
            unset($this->_object_set[$handle]);
        }

        return $this;
    }
    
    /**
     * Checks if the set contains a specific object
     *
     * @param   AnObjectHandlable $object
     * @return  bool Returns TRUE if the object is in the set, FALSE otherwise
     */
    public function contains(AnObjectHandlable $object)
    {
        $handle = $object->getHandle();
        return isset($this->_object_set[$handle]);
    }
    
    /**
     * Merge-in another object set
     *
     * @param   AnObjectSet  $set
     * @return  AnObjectQueue
     */
    public function merge(AnObjectSet $set)
    {
        foreach ($set as $object) {
            $this->insert($object);
        }

        return $this;
    }
    
    /**
     * Check if the object exists in the queue
     *
     * Required by interface ArrayAccess
     *
     * @param   AnObjectHandlable $object
     * @return  bool Returns TRUE if the object exists in the storage, and FALSE otherwise
     * @throws  InvalidArgumentException if the object doesn't implement AnObjectHandlable
     */
    public function offsetExists($object)
    {
        if (! $object instanceof AnObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement AnObjectHandlable');
        }

        return $this->contains($object);
    }
    
    /**
     * Returns the object from the set
     *
     * Required by interface ArrayAccess
     *
     * @param   AnObjectHandlable $object
     * @return  AnObjectHandlable
     * @throws  InvalidArgumentException if the object doesn't implement AnObjectHandlable
     */
    public function offsetGet($object)
    {
        if (! $object instanceof AnObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement AnObjectHandlable');
        }

        $handle = $object->getHandle();
        
        return $this->_object_set[$handle];
    }
    
    /**
     * Store an object in the set
     *
     * Required by interface ArrayAccess
     *
     * @param   AnObjectHandlable  $object
     * @param   mixed             $data The data to associate with the object [UNUSED]
     * @return  \AnObjectSet
     * @throws  InvalidArgumentException if the object doesn't implement AnObjectHandlable
     */
    public function offsetSet($object, $data)
    {
        if (! $object instanceof AnObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement AnObjectHandlable');
        }

        $this->insert($object);
        
        return $this;
    }
    
    /**
     * Removes an object from the set
     *
     * Required by interface ArrayAccess
     *
     * @param   AnObjectHandlable  $object
     * @return  \AnObjectSet
     * @throws  InvalidArgumentException if the object doesn't implement the AnObjectHandlable interface
     */
    public function offsetUnset($object)
    {
        if (! $object instanceof AnObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement AnObjectHandlable');
        }

        $this->extract($object);
        
        return $this;
    }
    
    /**
     * Return a string representation of the set
     *
     * Required by interface Serializable
     *
     * @return  string  A serialized object
     */
    public function serialize()
    {
        return serialize($this->_object_set);
    }

    /**
     * Unserializes a set from its string representation
     *
     * Required by interface Serializable
     *
     * @param   string  $serialized The serialized data
     */
    public function unserialize($serialized)
    {
        $this->_object_set = (array) unserialize($serialized);
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
        return count($this->_object_set);
    }

    /**
     * Return the first object in the set
     *
     * @return	mixed \AnObject or NULL is queue is empty
     */
    public function top()
    {
        return $this->_object_set[0];
        // return end($this->_object_set);
    }
    
    /**
     * Defined by IteratorAggregate
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_object_set);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return  array
     */
    public function rewind()
    {
        reset($this->_object_set);
        return $this;
    }
    
    /**
     * Checks if current position is valid
     *
     * @return  boolean
     */
    public function valid()
    {
        return !is_null(key($this->_object_set));
    }

    /**
     * Return the key of the current element
     *
     * @return  mixed
     */
    public function key()
    {
        return key($this->_object_set);
    }

    /**
     * Return the current element
     *
     * @return  mixed
     */
    public function current()
    {
        return $this->_object_set[$this->key()];
    }

    /**
     * Move forward to next element
     *
     * @return  void
     */
    public function next()
    {
        return next($this->_object_set);
    }
    
    /**
     * Return an associative array of the data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_object_set;
    }

    /**
     * Individually set the column value of each object.
     *
     * @param string $column The column to set a value for
     * @param mixed  $value  The column Value
     *
     * @return AnObjectSet
     */
    public function __set($column, $value)
    {
        foreach ($this as $object) {
            $object->$column = $value;
        }
    }

    /**
     * Retrieve an array of column values and return an array of
     * objects, scarlar or a single boolean value.
     *
     * @param  	string 	The column name.
     *
     * @return mixed
     */
    public function __get($column)
    {
        return $this->_forward('attribute', $column);
    }

    /**
     * Forwards the $method to each of the objects and return an array of
     * objects, scarlar or a single boolean value.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments = array())
    {
        if (isset($this->_mixed_methods[$method])) {
            return parent::__call($method, $arguments);
        }

        return $this->_forward('method', $method, $arguments);
    }
    
    /**
     * Preform a deep clone of the object
     *
     * @retun void
     */
    public function __clone()
    {
        parent::__clone();

        $this->_object_set = $this->_object_set;
    }

    /**
     * Forward a request to all the objects in the object set.
     *
     * @return mixed
     */
    protected function _forward($type, $callable, $arguments = array(), $return = null)
    {
        settype($arguments, 'array');

        $results = array();
        $is_object = true;
        $is_boolean = true;
        
        foreach ($this as $object) {
            if ($type == 'method') {
                $value = call_object_method($object, $callable, $arguments);
            } else {
                if (empty($object->$callable)) {
                    continue;
                } else {
                    $value = $object->$callable;
                }
            }

            if (! is_object($value)) {
                $is_object = false;
            }

            if (! is_bool($value)) {
                $is_boolean = false;
            }

            $results[] = $value;
        }

        if (empty($results)) {
            if ($return == 'array') {
                return array();
            } elseif ($return == 'boolean') {
                return false;
            }
        }

        if ($is_object) {
            $set = new self();
            
            foreach ($results as $value) {
                $set->insert($value);
            }
            
            $results = $set;
        } elseif ($is_boolean) {
            $results = array_unique($results);
            $value = true;
            
            foreach ($results as $result) {
                $value = $value && $result;
            }

            $results = $value;
        }

        return $results;
    }
}
