<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Exception
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Key based error object
 *
 * @category   Anahita
 * @package    Anahita_Exception
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnError extends KObject implements KObjectHandlable
{
    /**
     * Generic error codes
     */
    const INVALID_FORMAT = 'InvalidFormat';
    const INVALID_LENGTH = 'InvalidLength';
    const NOT_UNIQUE     = 'NotUnique';    
    const MISSING_VALUE  = 'MissingValue';
    const OUT_OF_SCOPE   = 'OutOfScope';
    
    /**
     * The error type 
     * 
     * @var String
     */
    protected $_code;
    
    /**
     * Message
     * 
     * @var String
     */
    protected $_message;
    
    /**
     * A key key
     * 
     * @string 
     */
    protected $_key;
    
    /**
     * Extra information regarding the error
     * 
     * @var array
     */
    protected $_data = array();
    
    /** 
     * Constructor.
     *
     * @param array $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct($config = array()) 
    {
       $config = new KConfig($config);
       
       $this->_message = $config->message; 
       $this->_code    = $config->code;
       $this->_key     = $config->key;
       
       unset($config['message']);
       unset($config['code']);
       unset($config['key']);
       
       $this->_data = $config->toArray();
    }
    
    /**
     * Return the key
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Return the message
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }
    
    /**
     * Return the reason
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }
    
    /**
     * Return the exception
     * 
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * Return an array represention of the error
     * 
     * @return array
     */
    public function toArray()
    {
        $data            = $this->_data;
        $data['message'] = $this->getMessage();
        $data['code']    = $this->getCode();
        $data['key']     = $this->getkey();
        $data = array_reverse($data);
        return $data;
    }
    
    /**
     * Gets the userinfo key
     * 
     * @param string $key Arbituary key key
     * 
     * @return void
     */
    public function __get($key)
    {
        $result = null;
        
        if ( isset($this->_data[$key]) ) {
            $result = $this->_data[$key];
        }
        
        return $result;
    }
}