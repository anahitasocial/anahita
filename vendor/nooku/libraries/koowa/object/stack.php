<?php
/**
 * @version     $Id: stack.php 4636 2012-05-13 16:36:26Z johanjanssens $
 * @package     Koowa_Template
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Object Stack Class
 *
 * Implements a simple stack collection (LIFO)
 *
 * @author     Johan Janssens <johan@nooku.org>
 * @package    Koowa_Object
 */
class KObjectStack extends KObject implements Countable
{
    /**
     * The object container
     *
     * @var array
     */
    protected $_object_stack = null;

    /**
     * Constructor
     *
     * @param KConfig|null $config  An optional KConfig object with configuration options
     * @return \KObjectStack
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_object_stack = array();
    }

    /**
     * Peeks at the element from the end of the stack
     *
     * @return mixed The value of the top element
     */
    public function top()
    {
        return end($this->_object_stack);
    }

    /**
     * Pushes an element at the end of the stack
     *
     * @param   KObject $object
     * @return \KObjectStack
     * @throws  InvalidArgumentException if the object doesn't extend from KObject
     */
    public function push($object)
    {
        if(!$object instanceof KObject) {
           // throw new InvalidArgumentException('Object needs to extend from KObject');
        }

        $this->_object_stack[] = $object;
        return $this;
    }

    /**
     * Pops an element from the end of the stack
     *
     * @return  mixed The value of the popped element
     */
    public function pop()
    {
        return array_pop($this->_object_stack);
    }

    /**
     * Counts the number of elements
     *
     * @return integer  The number of elements
     */
    public function count()
    {
        return count($this->_object_stack);
    }

    /**
     * Check to see if the registry is empty
     *
     * @return boolean  Return TRUE if the registry is empty, otherwise FALSE
     */
    public function isEmpty()
    {
        return empty($this->_object_stack);
    }
}