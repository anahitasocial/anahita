<?php
/**
 * @package     Anahita_Object
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
class AnObjectStack extends AnObject implements Countable
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
     * @param AnConfig|null $config  An optional AnConfig object with configuration options
     * @return \AnObjectStack
     */
    public function __construct(AnConfig $config)
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
     * @param   AnObject $object
     * @return \AnObjectStack
     * @throws  InvalidArgumentException if the object doesn't extend from AnObject
     */
    public function push($object)
    {
        if (!$object instanceof AnObject) {
            // throw new InvalidArgumentException('Object needs to extend from AnObject');
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
