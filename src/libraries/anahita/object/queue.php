<?php
/**
 * @package     Anahita_Object
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
class AnObjectQueue extends AnObject implements Iterator, Countable
{
    /**
     * Object list
     *
     * @var array
     */
    protected $_object_list = array();

    /**
     * Priority list
     *
     * @var array
     */
    protected $_priority_list = array();

    /**
     * Constructor
     *
     * @param AnConfig|null $config  An optional AnConfig object with configuration options
     * @return \AnObjectQueue
     */
    public function __construct(AnConfig $config = null)
    {
        //If no config is passed create it
        if (! isset($config)) {
            $config = new AnConfig();
        }

        parent::__construct($config);

        // $this->_object_list = new ArrayObject();
        // $this->_priority_list = new ArrayObject();
    }

    /**
     * Inserts an object to the queue.
     *
     * @param   AnObjectHandlable  $object
     * @param   integer           $priority
     * @return  boolean		TRUE on success FALSE on failure
     * @throws  InvalidArgumentException if the object doesn't implement AnObjectHandlable
     */
    public function enqueue(AnObjectHandlable $object, $priority = AnCommand::PRIORITY_NORMAL)
    {
        // $result = false;

        if ($handle = $object->getHandle()) {
            
            $this->_object_list[$handle] = $object;
            //$this->_object_list->offsetSet($handle, $object);
            
            $this->_priority_list[$handle] = $priority;
            // $this->_priority_list->offsetSet($handle, $priority);
            asort($this->_priority_list);
            // $this->_priority_list->asort();

            return true;
            // $result = true;
        }

        return false;
        // return $result;
    }

    /**
     * Removes an object from the queue
     *
     * @param   AnObjectHandlable $object
     * @return  boolean	TRUE on success FALSE on failure
     * @throws  InvalidArgumentException if the object implement AnObjectHandlable
     */
    public function dequeue(AnObjectHandlable $object)
    {
        // $result = false;

        if ($handle = $object->getHandle()) {
            if (isset($this->_object_list[$handle])) {
                unset($this->_object_list[$handle]);
                unset($this->_priority_list[$handle]);
                
                return true;
            }
            
            /*
            if ($this->_object_list->offsetExists($handle)) {
                $this->_object_list->offsetUnset($handle);
                $this->_priority_list->offsetUnSet($handle);

                $result = true;
            }
            */
        }

        return false;
    }

    /**
     * Set the priority of an object in the queue
     *
     * @param   AnObjectHandlable  $object
     * @param   integer           $priority
     * @return  AnCommandChain
     * @throws  InvalidArgumentException if the object doesn't implement AnObjectHandlable
     */
    public function setPriority(AnObjectHandlable $object, $priority = AnCommand::PRIORITY_NORMAL)
    {
        if ($handle = $object->getHandle()) {
            if (isset($this->_priority_list[$handle])) {
                $this->_priority_list[$handle] = $priority;
                asort($this->_priority_list);
            }
            /*
            if ($this->_priority_list->offsetExists($handle)) {
                $this->_priority_list->offsetSet($handle, $priority);
                $this->_priority_list->asort();
            }
            */
        }

        return $this;
    }

    /**
     * Get the priority of an object in the queue
     *
     * @param   AnObjectHandlable $object
     * @return  integer|false The command priority or FALSE if the commnand isn't enqueued
     * @throws  InvalidArgumentException if the object doesn't implement AnObjectHandlable
     */
    public function getPriority(AnObjectHandlable $object)
    {
        // $result = false;

        if ($handle = $object->getHandle()) {
            // if ($this->_priority_list->offsetExists($handle)) {
            //    $result = $this->_priority_list->offsetGet($handle);
            // }
            if (isset($this->_priority_list[$handle])) {
                return $this->_priority_list[$handle];
            }
        }

        return false;
        // return $result;
    }

    /**
     * Check if the queue has an item with the given priority
     *
     * @param  integer  $priority   The priority to search for
     * @return boolean
     */
    public function hasPriority($priority)
    {
        return array_search($priority, $this->_priority_list);
    }

    /**
     * Check if the queue does contain a given object
     *
     * @param  AnObjectHandlable $object
     * @return bool
     * @throws  InvalidArgumentException if the object doesn't implement AnObjectHandlable
     */
    public function contains(AnObjectHandlable $object)
    {
        // $result = false;
        
        $handle = $object->getHandle();
        return isset($this->_object_list[$handle]);

        // if ($handle = $object->getHandle()) {
            // $result = $this->_object_list->offsetExists($handle);
        // }

        // return $result;
    }

    /**
     * Returns the number of elements in the queue
     *
     * Required by the Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_object_list);
    }

    /**
     * Rewind the Iterator to the top
     *
     * Required by the Iterator interface
     *
     * @return	object AnObjectQueue
     */
    public function rewind()
    {
        reset($this->_object_list);
        reset($this->_priority_list);

        return $this;
    }

    /**
     * Check whether the queue contains more object
     *
     * Required by the Iterator interface
     *
     * @return	boolean
     */
    public function valid()
    {
        return !is_null(key($this->_priority_list));
    }

    /**
     * Return current object index
     *
     * Required by the Iterator interface
     *
     * @return	mixed
     */
    public function key()
    {
        return key($this->_priority_list);
    }

    /**
     * Return current object pointed by the iterator
     *
     * Required by the Iterator interface
     *
     * @return	mixed
     */
    public function current()
    {
        return $this->_object_list[$this->key()];
    }

    /**
     * Move to the next object
     *
     * Required by the Iterator interface
     *
     * @return	void
     */
    public function next()
    {
        return next($this->_priority_list);
    }

    /**
     * Return the object from the top of the queue
     *
     * @return	AnObject or NULL is queue is empty
     */
    public function top()
    {
        $handles = array_keys((array) $this->_priority_list);
        $object = null;
        
        if (isset($handles[0])) {
            $object = $this->_object_list[$handles[0]];
        }

        return $object;
    }

    /**
     * Checks whether the queue is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !count($this->_object_list);
    }

    /**
     * Preform a deep clone of the object
     *
     * @return void
     */
    /* 
    public function __clone()
    {
        parent::__clone();

        $this->_object_list = clone $this->_object_list;
        $this->_priority_list = clone $this->_priority_list;
    }
    */
}
