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
 * It's the same as {@link KObjectSet} but on __get, __set and __call method calls
 * it iterates through its members to perform the same calls
 * 
 * For example
 * 
 * <code>
 * $object1 = new KObject();
 * $object->name = 'This is object 1';
 * $object2 = new KObject();
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
 * @package    Anahita_Object
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnObjectSet extends KObjectSet
{
    /**
     * Constructor
     *
     * @param KConfig|null $config  An optional KConfig object with configuration options
     * @return \KObjectSet
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();

        parent::__construct($config);
                    
        if ( $config->data ) {
            foreach($config->data as $object) 
                $this->insert($object);
        }
        
    }
        
    /**
     * Individually set the column value of each object
     *
     * @param string $column The column to set a value for
     * @param mixed  $value  The column Value
     *  
     * @return 	AnObjectSet
     */
    public function __set($column, $value)
    {
        foreach($this as $object) {
            $object->$column = $value;
        }
    }   
     
	/**
     * Retrieve an array of column values and return an array of
	 * objects, scarlar or a single boolean value
     *
     * @param  	string 	The column name.
     * @return 	mixed 	
     */
    public function __get($column)
    {
 		return $this->_forward('attribute', $column);
    }
    	
	/**
	 * Forwards the $method to each of the objects and return an array of
	 * objects, scarlar or a single boolean value
	 * 
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments = array())
	{
		if ( isset($this->_mixed_methods[$method]) )	
        {	
			return parent::__call($method, $arguments);
        }        
		return $this->_forward('method', $method, $arguments);
	}
	
	/**
	 * Forward a request to all the objects in the object set
	 * 
	 * @return mixed
	 */
	protected function _forward($type, $callable, $arguments = array(), $return = null)
	{
		settype($arguments, 'array');
		
   		$results 	= array();
    	$is_object  = true;
    	$is_boolean = true;
    	foreach($this as $object)
        {
            if ( $type == 'method' )
            	$value = call_object_method($object, $callable, $arguments);
            else 
            {
            	if ( empty($object->$callable) ) 
            		continue;
            	else $value = $object->$callable;
            }
                        
 	        if ( !is_object($value) )
 	         	$is_object = false;  
 	         		        
 	        if ( !is_bool($value))
 	         	$is_boolean = false;
 	         	 
 	        $results[] = $value;	         	           
        }
        
        if ( empty($results) ) {
        	if ( $return == 'array' )
        		return array();
        	elseif ( $return == 'boolean' )
        		return false;
        }
        
		if ( $is_object ) 
		{
			$set = new self();
			foreach($results as $value)
				$set->insert($value);
			$results = $set;
		} else if ( $is_boolean )
        {
        	$results = array_unique($results);
        	$value	 = true;
        	foreach($results as $result)
        		$value = $value && $result;
        		
        	$results   = $value;
        }
		
    	return $results;		
	}
}