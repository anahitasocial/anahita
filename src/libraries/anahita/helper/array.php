<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

define('PHP_INT_MIN', ~PHP_INT_MAX); 

/**
 * Array Helper
 *
 * @category   Anahita
 * @package    Anahita_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnHelperArray extends KHelperArray
{			
	/**
	 * Index flags
	 */
	const LAST_INDEX  = PHP_INT_MAX;
	const FIRST_INDEX = PHP_INT_MIN;
		
	/**
	 * Index an object (or an array) using one of it's attribute
	 * 
	 * @param array  $items   An array of object or associative array  
	 * @param string $key 	  Attribute by which to index the array
	 * 
	 * @return array
	 */
	public static function indexBy($items, $key)
	{
		$array = array();

		foreach($items as $item) 
		{
			$array[self::getValue($item, $key)] = $item;
		}
		
		return $array;
	}
	
	/**
	 * Collects $key from an array of items
	 * 
	 * @param array  		$items An array of object or associative array  
	 * @param string|array  $key   The key to collect the value for
	 * 
	 * @return array
	 */
	static public function collect($items, $key)
	{
		$array = array();
			
		foreach($items as $v) 
		{
			if ( is_array($key) ) {
				foreach($key as $index => $k) {
					if ( !isset($array[$k]) ) {
						$array[$k] = array();
					}
					$array[$k][] = self::getValue($v, $k);
				}
			}
			else $array[] = self::getValue($v, $key);
		}
			
		return $array;
	}
	
	/**
	 * Groups an array of items by their common $key
	 * 
	 * @param array  $items  An array of object or associative array  
	 * @param string $key    Attribute by which to index the array
	 * 
	 * @return array
	 */
	public static function groupBy($items, $key)
	{
		$array = array();
		
		foreach($items as $item) 
		{
			$value = self::getValue($item, $key);
			if ( !isset($array[$value]) ) {
				$array[$value] = array();
			}
			$array[$value][] = $item;
		}
		
		return $array;
	}
	
	/**
	 * Return a unique array of $array. This method also handles object as value
	 * 
	 * @param array $array An Array
	 * 
	 * @return array
	 */
	static public function unique($array)
	{
		$unique = array();
		foreach($array as $item) {
			if (!in_array($item, $unique, true)) {
				$unique[] = $item;
			}
		}
		return $unique;
	}	
	
	/**
	 * Insert into an array. If no offset is given then the values
	 * are inserted at the end of the list. Returns the new array with
	 * value inserted into
	 *
	 * @param array $array  The orignal array
	 * @param array $values An array of values to be inserted
	 * 
	 * @return array
	 */
	static public function insert($array, $values, $index = null)
	{
		$values = (array)KConfig::unbox($values);
		$array  = (array)KConfig::unbox($array);
		if ( $index === null ) {
			foreach($values as $value) {				
				array_push($array, $value);
			}		
		} else {
			array_splice($array, $index, 0, $values);			
		}
		return $array;
	}
	
	/**
	 * Unset a list of values from an array. This method both unset any key that exists
	 * in the $values array as well as any values that exists in the $values array. This method
	 * modifies the $array object 
	 * 
	 * @param array $array An Array values to unset
	 * 
	 * @return array
	 */
	static public function unsetValues($array, $values)
	{
		settype($values, 'array');		
		
		foreach($array as $index => $item) 
		{
			foreach($values as $value)  
            {
				if ( !is_numeric($index) ) {
                    $item = $index;
                }
                
                if ( $value == $item ) {				    
					unset($array[$index]);
				}
			}
		}
		return $array;
	}	
		
	/**
	 * Flattens a multi-dimensial array and return all the values as one single array 
	 * 
	 * @param array $array The array to be flattened
	 * 
	 * @return array
	 */
	static public function getValues($array)
	{	
	    settype($array,'array');
	    $values = array();
	    foreach($array as $key => $value)
	    {
	    	if ( is_array($value) ) {
	    		$values = array_merge($values, self::getValues($value));
	    	} else
	    		$values[] = $value;
	    }
	    return $values;
	}
	
	/**
	 * Return the simple scalar array of the object
	 * 
	 * @param mixed $object An object to be converted to an array
	 * 
	 * @return array
	 */
	public static function toArray($object)
	{
		$object = KConfig::unbox($object);
		
		if ( is($object, 'KObjectArray') || is($object, 'KObjectSet') )
			return $object->toArray();
			
		return (array) $object;
	}	
	
	/**
	 * Get the value of an item (array|object) using a $key. The $key can be a string path
	 * 
	 * @param object $item The object whose attribute value is being returend
	 * @param string $key  The attribute name
	 * 
	 * @return mixed
	 */
	public static function getValue($item, $key)
	{
		$parts = explode('.', $key);		
		$value = $item;
		foreach($parts as $part) 
		{
			if ( $value ) {
				if ( is($value, 'KObject') )
					$value = $value->get($part);
				else
					$value = is_array($value) ? $value[$part] : $value->$part;				
			}
		}
		return $value;
	}
	
	/**
	 * Return an interator for an object
	 *
	 * @param mixed $object An Iteratable or NonInterable object
	 * 
	 * @return Iteratorable
	 */
	public static function getIterator($object)
	{
		if ( !self::isIterable($object) )
			$object = array($object);
			
		return $object;
	}
	
	/**
	 * Get the value at an index. The index can be an integer or 'first' or 'last'. If the
	 * index doesn't exists it returns null
	 * 
	 * @param array $array
	 * @param mixed $index
	 * 
	 * @return null or value
	 */
	public static function getValueAtIndex($array, $index)
	{
		$value = null;
		
		if ( abs((int)$index) == self::LAST_INDEX ) {
			$index == self::LAST_INDEX ? end($array) : reset($array);
			$value = current($array);
		} else if ( isset($array[$index]) ) {
			$value = $array[$index];
		}
		
		return $value;
	}
	
	/**
	 * Return true if the array is some of kind of iterative array. 
	 * 
	 * @param array $array An array of object or associative array  
	 * 
	 * @return boolean
	 */
	public static function isIterable($array)
	{
		return is_array($array) || $array instanceof Iterator;
	}
}