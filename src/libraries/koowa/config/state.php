<?php
/**
 * @version		$Id: state.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * State Config Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Config
 */
class KConfigState extends KConfig
{
	/**
     * Retrieve a configuration item and return $default if there is no element set.
     *
     * @param string
     * @param mixed
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if(isset($this->_data[$name])) {
            $result = $this->_data[$name]->value;
        }

        return $result;
    }

    /**
     * Set state value
     *
     * @param  	string 	The user-specified state name.
     * @param  	mixed  	The user-specified state value.
     * @return 	void
     */
    public function __set($name, $value)
    {
    	if(isset($this->_data[$name])) {
    		$this->_data[$name]->value = $value;
    	}
    }

    /**
     * Unset a state value
     *
     * @param   string  The column key.
     * @return  void
     */
    public function __unset($name)
    {
        if(isset($this->_data[$name])) {
            $this->_data[$name]->value = $this->_data[$name]->default;
        }
    }

    /**
     * Insert a new state
     *
     * @param   string      The name of the state
     * @param   mixed       Filter(s), can be a KFilterInterface object, a filter name or an array of filter names
     * @param   mixed       The default value of the state
     * @param   boolean     TRUE if the state uniquely indetifies an enitity, FALSE otherwise. Default FALSE.
     * @param   array       Array of required states to determine if the state is unique. Only applicable if the state is unqiue.
     * @return  KConfigState
     */
    public function insert($name, $filter, $default = null, $unique = false, $required = array())
    {
        $state = new stdClass();
        $state->name     = $name;
        $state->filter   = $filter;
        $state->value    = $default;
        $state->unique   = $unique;
        $state->required = $required;
        $state->default  = $default;
        $this->_data[$name] = $state;

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
        unset($this->_data[$name]);
        return $this;
    }

    /**
     * Reset all state data and revert to the default state
     *
     * @param   boolean If TRUE use defaults when resetting. Default is TRUE
     * @return KConfigState
     */
    public function reset($default = true)
    {
        foreach($this->_data as $state) {
            $state->value = $default ? $state->default : null;
        }

        return $this;
    }

     /**
     * Set the state data
     *
     * This function will only filter values if we have a value. If the value
     * is an empty string it will be filtered to NULL.
     *
     * @param   array|object    An associative array of state values by name
     * @return  KConfigState
     */
    public function setData(array $data)
    {
        // Filter data
        foreach($data as $key => $value)
        {
            if(isset($this->_data[$key]))
            {
                $filter = $this->_data[$key]->filter;

                //Only filter if we have a value
                if($value !== null)
                {
                    if($value !== '')
                    {
                        if(!($filter instanceof KFilterInterface)) {
                            $filter = KService::get('koowa:filter.factory')->instantiate($filter);
                        }

                        $value = $filter->sanitize($value);
                    }
                    else $value = null;

                    $this->_data[$key]->value = $value;
                }
            }
        }

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

        foreach ($this->_data as $name => $state)
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
                            if(!$this->_validate($this->_data[$required]))
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
                                $data[$required] = $this->_data[$required]->value;
                            }
                        }
                    }
                }
                else $data[$name] = $state->value;
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
     * Check if the state information is empty
     *
     * @param   array   An array of states names to exclude.
     * @return  boolean TRUE if the state is empty, otherwise FALSE.
     */
    public function isEmpty(array $exclude = array())
    {
        $states = $this->getData();

        foreach($exclude as $state) {
            unset($states[$state]);
        }

        return (bool) (count($states) == 0);
    }

	/**
     * Return an associative array of the states.
     *
     * @param bool 	If TRUE return only as associative array of the state values. Default is TRUE.
     * @return array
     */
    public function toArray($values = true)
    {
        if($values)
        {
            $result = array();
            foreach($this->_data as $state) {
                $result[$state->name] = $state->value;
            }
        }
        else $result = $this->_data;

        return $result;
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