<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Application
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Flash memory. Can contain key value pairs
 *
 * @category   Anahita
 * @package    Com_Application
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComApplicationControllerBehaviorMessageFlash extends ArrayObject
{
    /**
     * A flash can contain only one message
     * 
     * @param boolean $unset Unset the message after getting it
     * 
     * @return array
     */
    public function getMessage($unset = false)
    {
        $message = $this->offsetGet('message');
        
        if ( $unset ) {
            unset($this['message']);
        }
        
        return $message; 
    }
    
    /**
     * Unsets an offset
     * 
     * (non-PHPdoc)
     * @see ArrayObject::offsetUnset()
     */
    public function offsetUnset($key)
    {
        if ( $this->offsetExists($key) ) {
            parent::offsetUnset($key);
        }
    }
    
    /**
     * Return null if the offset doesn't exits
     * 
     * (non-PHPdoc)
     * @see ArrayObject::offsetGet()
     */
    public function offsetGet($key)
    {
        if ( $this->offsetExists($key) ) {
            return parent::offsetGet($key);
        }
        return null;
    }
    
    /**
     * Forward to offsetGet
     * 
     * @param string $key
     * 
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }
    
    /**
     * Forward to offsetGet
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        if ( $key == 'message' ) {
            return $this->getMessage();
        }
        else 
            return $this[$key] = $value;
    }

    /**
     * Forward to offsetGet
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __isset($key)
    {
        return isset($this[$key]);
    }    
    
    /**
     * Forwards to unset
     * 
     * @param string $key
     * 
     * @return mixed
     */
    public function __unset($key)
    {
        unset($this[$key]);
    }
}