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
 * It's the same as {@link KObjectArray} but it allows to use {@link KObjectHandlable} as
 * keys
 * 
 * <code>
 * $array  = new AnObjectArray();
 * $object = new KObject();
 * $array[$object] = 'Some Value';
 * </code>
 *
 * @category   Anahita
 * @package    Anahita_Object
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnObjectArray extends KObjectArray
{	
    /**
     * Get a value by key
     *
     * @param   string  The key name.
     * @return  string  The corresponding value.
     */
    public function __get($key)
    {    		
        $result = null;
        $key 	= $this->__key($key);
        if(isset($this->_data[$key])) {
            $result = $this->_data[$key];
        } 
        
        return $result;
    }

    /**
     * Set a value by key
     *
     * @param   string  The key name.
     * @param   mixed   The value for the key
     * @return  void
     */
    public function __set($key, $value)
    {
       $this->_data[ $this->__key($key) ] = $value;
     }
   
	/**
     * Test existence of a key
     *
     * @param  string  The key name.
     * @return boolean
     */
    public function __isset($key)
    {
        return array_key_exists($this->__key($key), $this->_data);
    }

    /**
     * Unset a key
     * 
     * @param   string  The key name.
     * @return  void
     */
    public function __unset($key)
    {
         unset($this->_data[ $this->__key($key) ]);
    }

    /**
     * Return a key
     *
     * @param  mixed $key
     * @return string
     */
    private function __key($key)
    {
    	if ( $key instanceof KObjectHandlable )
    		$key = $key->getHandle();
    	elseif ( gettype($key) == 'object' )
    		$key = spl_object_hash($key);
    		
    	return $key;    	
    }
}