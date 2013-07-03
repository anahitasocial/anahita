<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 *  Contains a set of template objects like gadgets, composers and commands.
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateObjectContainer implements IteratorAggregate, Countable, ArrayAccess
{
	/**
	 * Objects
	 * 
	 * @var array
	 */
	protected $_objects = array();

	/**
	 * Inserts a template object by providing an array of $data
	 *
	 * @param string  The key name.
	 * @param array   The value for the key
	 *
	 * @return  LibBaseTemplateObject
	 * @throws  KConfigException if the $name is empty
	 */
	public function insert($name, array $data)
	{
	    return $this->offsetSet($name, $data);
	}	
	
	/**
	 * Unses a object from the queue using its key and returns it. If an array of
	 * names is passed then a new container is returned. If no name is passed then 
     * return the top element
	 * 
	 * @param string|array $name The object name or an array of object names. If name is null
     *                           then return the first element 
	 * 
	 * @return LibBaseTemplateObject|LibBaseTemplateObjectContainer
	 */
	public function extract($name=null)
	{
        if ( $name instanceof LibBaseTemplateObjectInterface)
        {
            $name = $name->getName(); 
        }
        
        if ( is_null($name) )
        {
            return array_shift($this->_objects);
        }
	    elseif ( is_array($name) )
	    {
	        $container = clone $this;
	        $container->setObjects(array());
	        foreach($name as $key) {
	            if ( $object = $this->extract($key) ) {
	                $container[] = $object;
	            }
	        }
	        return $container;
	    }
		else 
		{
		    $object = null;
		    
		    if ( isset($this->_objects[$name]) )
		    {
		        $object = $this->_objects[$name];
		        unset($this->_objects[$name]);
		    }
		    return $object;		    
		}
	}
	
	/**
	 * Return the count of objects
	 * 
	 * @return int
	 */
	public function count()
	{
		return count($this->_objects);
	}
	
	/**
	 * Return ArrayIterator
	 * 
	 * @return ArrayIterator
	 */
  	public function getIterator() 
  	{
        return new ArrayIterator($this->_objects);
    }
    
    /**
     * Get a value by key
     *
     * @param   string  The key name.
     * @return  string  The corresponding value.
     */
    public function offsetGet($key)
    {
        $result = null;
        if(isset($this->_objects[$key])) {
            $result = $this->_objects[$key];
        }    
        return $result;
    }
    
    /**
     * Set a value by key
     *
     * @param   string  The key name.
     * @param   mixed   The value for the key
     * 
     * @return  void
     * @throws KConfigException if the $name is empty
     */
    public function offsetSet($name, $object)
    {
        if ( !$object instanceof LibBaseTemplateObjectInterface )
        {
            if ( empty($name) ) {
                throw new KConfigException('Template object name must be unique and non empty');    
            }
            
            $object = LibBaseTemplateObject::getInstance($name, $object);
        }
        
        $this->_objects[$object->getName()] = $object;
        
        return $object;
    }
    
    /**
     * Test existence of a key
     *
     * @param  string  The key name.
     * @return boolean
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->_objects);
    }
    
    /**
     * Unset a key
     *
     * @param   string  The key name.
     * @return  void
     */
    public function offsetUnset($key)
    {
        return $this->extract($key);
    }
    
    /**
     * Iteratively calls the $method on the all the template objects
     *
     * @param string $method    Method name
     * @param array  $arguments Array of arguments
     * 
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        foreach($this->getObjects() as $object )
        {
            call_object_method($object, $method, $arguments);
        }
        return $this;
    }
    
    /**
     * Reset
     *
     * @return void
     */
    public function reset()
    {
        $this->_objects = array();
    }
    
    /**
     * Rearrange the objects according to the order arrangement. If head is set to true
     * then the sort order is applied to the head of the objects if false then it's applied 
     * to the tail
     *
     * @param array $order An array of order=>name
     * @param bool  $head  Whether to apply the sort order to the head of array or tail 
     * 
     * @return LibBaseTemplateObjectContainer
     */
    public function sort($order, $head = true)
    {        
        $list = array();
        
        settype($order, 'array');
        
        $order = array_unique($order);
                
        foreach($order as $item) {
            if ( isset($this->_objects[$item]) ) {
                $list[$item]  = $this->_objects[$item];
            } 
        }
        
        foreach($this->_objects as $key => $object) {
            if ( isset($list[$key]) ) {
                unset($this->_objects[$key]);       
            }
        }
        
        
        if ( $head ) {
            $objects = array_merge($list, $this->_objects);
        } else {
            $objects = array_merge($this->_objects, $list);
        }
        
        $this->_objects = $objects; 
        
        return $this;       
    }
    
    /**
     * Set all the objects
     *
     * @param array $objects
     * 
     * @return void
     */
    public function setObjects(array $objects)
    {
        $this->_objects = array();
        
        foreach($objects as $object) {
            $this[] = $object;
        }
    }
    
    /**
     * Return array of objects
     *
     * @return array
     */
    public function getObjects()
    {
        return $this->_objects;
    }
    
    /**
     * If a container is cloned then clone all the objects
     * 
     * @return void
     */
    public function __clone()
    {
      foreach($this->_objects as $key => $object)
      {
          $this->_objects[$key] = clone $object;
      }
    }

}