<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Object
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * It's the same as {@link KObjectDecorator} but implements some of the PHP interfaces
 * and forward the calls to the object 
 *  
 * @category   Anahita
 * @package    Anahita_Object
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnObjectDecorator extends KObjectDecorator implements Iterator, ArrayAccess, Countable, Serializable
{
    /**
     * Defined by IteratorAggregate
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->getObject()->getIterator();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return  \KObjectArray
     */
    public function rewind()
    {
        $this->getObject()->rewind();
        return $this;
    }

    /**
     * Checks if current position is valid
     *
     * @return  boolean
     */
    public function valid()
    {
        return $this->getObject()->valid();
    }

    /**
     * Return the key of the current element
     *
     * @return  mixed
     */
    public function key()
    {
        return $this->getObject()->key();
    }

    /**
     * Return the current element
     *
     * @return  mixed
     */
    public function current()
    {
        return $this->getObject()->current();
    }

    /**
     * Move forward to next element
     *
     * @return  void
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
     * Check if the object exists in the queue
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
     * Returns the object from the set
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
     * Store an object in the set
     *
     * Required by interface ArrayAccess
     *
     * @param   KObjectHandlable  $offset
     * @param   mixed             $data 
     */
    public function offsetSet($offset, $data)
    {
        $this->getObject()->offsetSet($offset, $data);
    }

    /**
     * Removes an object from the set
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
     * Return a string representation of the set
     *
     * Required by interface Serializable
     *
     * @return  string  A serialized object
     */
    public function serialize()
    {
        return $this->getObject()->serialize();
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
        $this->getObject()->unserialize($serialized);
    }     
}