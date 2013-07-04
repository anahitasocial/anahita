<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Controller State
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerState extends KConfig
{ 
    /**
     * States
     * 
     * @var array
     */
    protected $_states = array();
    
    /**
     * The entity/entityset
     * 
     * @return AnDomainEntityAbstract|AnDomainEntityset 
     */ 
    protected $_item;
    
    /**
     * List resource
     * 
     * @return AnDomainEntityset
     */
    protected $_list;
    
    /**
     * Set the item
     * 
     * @param mixed $item The item 
     * 
     * @return LibBaseControllerData
     */
    public function setItem($item)
    {
        $this->_item = $item;
        return $this;
    }
    
    /**
     * Return the item
     * 
     * @return mixed
     */
    public function getItem()
    {
        return $this->_item;
    }
    
    /**
     * Set the list
     * 
     * @param mixed List items
     * 
     * @return LibBaseControllerData
     */
    public function setList($list)
    {
        $this->_list = $list;
        return $this;
    }
    
    /**
     * Return list
     * 
     * @return mixed
     */
    public function getList()
    {
        return $this->_list;
    }

    /**
     * Insert a new state. This state can uniquely identify a resources or set of resoruces
     *
     * @param   string      The name of the state
     * @param   mixed       The default value of the state
     * @param   boolean     TRUE if the state uniquely indetifies an enitity, FALSE otherwise. Default FALSE.
     * @param   array       Array of required states to determine if the state is unique. Only applicable if the state is unqiue.
     * @return  KConfigState
     */
    public function insert($name, $default = null, $unique = false, $required = array())
    {
        $state = new stdClass();
        $state->name     = $name;
        $state->value    = pick($this->$name, $default);
        $state->unique   = $unique;
        $state->required = $required;
        $state->default  = $default;
        $this->_states[$name] = $state;
        
        //if a default value set, then try
        //set it to the state data
        if ( $this->_validate($state) ) {
            $this->append(array(
                $state->name => $state->value
            ));            
        }
        
        return $this;
    }
    
    /**
     * Remove an existing state
     *
     * @param   string      The name of the state
     * @return  KConfigState
     */
    public function remove( $name )
    {
        unset($this->_states[$name]);
        return $this;
    }    
    
    /**
     * Get the state data
     *
     * This function only returns states that have been been set.
     *
     * @param   boolean If TRUE only retrieve unique state values, default FALSE
     * @return  array   An associative array of state values by name
     */
    public function getData($unique = false)
    {
        $data = array();

        foreach ($this->_states as $name => $state)
        {
            if(isset($state->value))
            {
                //Only return unique data
                if($unique)
                 {
                    //Unique values cannot be null or an empty string
                    if($state->unique && $this->_validate($state))
                    {
                        $result = true;

                        //Check related states to see if they are set
                        foreach($state->required as $required)
                        {
                            if(!$this->_validate($this->_states[$required]))
                            {
                                $result = false;
                                break;
                            }
                        }

                        //Prepare the data to be returned. Include states
                        if($result)
                        {
                            $data[$name] = $state->value;

                            foreach($state->required as $required) {
                                $data[$required] = $this->_states[$required]->value;
                            }
                        }
                    }
                }
                else { 
                    $data[$name] = $state->value;
                }
            }
        }

        return $data;        
    }
    
    /**
     * Check if the state information is unique
     *
     * @return  boolean TRUE if the state is unique, otherwise FALSE.
     */
    public function isUnique()
    {
        $unique = false;

        //Get the unique states
        $states = $this->getData(true);

        if(!empty($states))
        {
            $unique = true;

            //If a state contains multiple values the state is not unique
            foreach($states as $state)
            {
                if(is_array($state) && count($state) > 1)
                {
                    $unique = false;
                    break;
                }
            }
        }

        return $unique;
    }    
    
    /**
     * Set state value
     *
     * @param   string  The user-specified state name.
     * @param   mixed   The user-specified state value.
     * @return  void
     */
    public function __set($name, $value)
    {
        if(isset($this->_states[$name])) {
            $this->_states[$name]->value = $value;
        }
        return parent::__set($name, $value);
    }

    /**
     * Unset a state value
     *
     * @param   string  The column key.
     * @return  void
     */
    public function __unset($name)
    {
        if(isset($this->_states[$name])) {
            $this->_states[$name]->value = $this->_states[$name]->default;
        }
        return parent::__unset($name);
    }
    
    /**
     * Supports a simple form Fluent Interfaces. Allows you to set states by
     * using the state name as the method name.
     *
     * For example : $model->sort('name')->limit(10)->getList();
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     * @return  KModelAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        if( count($args) > 0 ) {
            $this->__set(KInflector::underscore($method), $args[0]);
            return $this;
        }
         
        throw new BadMethodCallException('Call to undefined method :'.$method);
    }
    
    /**
     * Validate a unique state.
     *
     * @param  object  The state object.
     * @return boolean True if unique state is valid, false otherwise.
     */
    protected function _validate($state)
    {
        // Unique values can't be null or empty string.
        if(empty($state->value) && !is_numeric($state->value)) {
            return false;
        }

        if(is_array($state->value))
        {
            // The first element of the array can't be null or empty string.
            $first = array_slice($state->value, 0, 1);
            if(empty($first) && !is_numeric($first)) {
                return false;
            }
        }

        return true;
    }    
}