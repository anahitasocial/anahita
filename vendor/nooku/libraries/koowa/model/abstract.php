<?php
/**
 * @version		$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract Model Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Model
 * @uses		KObject
 */
abstract class KModelAbstract extends KObject
{
	/**
	 * A state object
	 *
	 * @var object
	 */
	protected $_state;

	/**
	 * List total
	 *
	 * @var integer
	 */
	protected $_total;

	/**
	 * Model list data
	 *
	 * @var array
	 */
	protected $_list;

	/**
	 * Model item data
	 *
	 * @var mixed
	 */
	protected $_item;

	/**
	 * Model column data
	 *
	 * @var mixed
	 */
	protected $_column;

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config = null)
	{
        //If no config is passed create it
		if(!isset($config)) $config = new KConfig();

		parent::__construct($config);

		$this->_state = $config->state;
	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
            'state' => new KConfigState(),
       	));

       	parent::_initialize($config);
    }

	/**
	 * Test the connected status of the model.
	 *
	 * @return	boolean	Returns TRUE by default.
	 */
    public function isConnected()
	{
	    return true;
	}

	/**
     * Set the model state properties
     *
     * This function overloads the KObject::set() function and only acts on state properties it
     * will reset (unsets) the $_list, $_item and $_total model properties when a state changes.
     *
     * @param   string|array|object	The name of the property, an associative array or an object
     * @param   mixed  				The value of the property
     * @return	KModelAbstract
     */
    public function set( $property, $value = null )
    {
    	$changed = false;

        if(is_object($property)) {
    		$property = (array) KConfig::unbox($property);
    	}

        if(is_array($property))
        {
            foreach($property as $key => $value)
            {
                if(isset($this->_state->$key) && $this->_state->$key != $value)
                {
                    $changed = true;
                    break;
                }
            }

        	$this->_state->setData($property);
        }
        else
        {
            if(isset($this->_state->$property) && $this->_state->$property != $value) {
                $changed = true;
            }

            $this->_state->$property = $value;
        }

        if($changed)
        {
            $this->_list  = null;
            $this->_item  = null;
            $this->_total = null;
        }

        return $this;
    }

    /**
     * Get the model state properties
     *
     * This function overloads the KObject::get() function and only acts on state
     * properties
     *
     * If no property name is given then the function will return an associative
     * array of all properties.
     *
     * If the property does not exist and a  default value is specified this is
     * returned, otherwise the function return NULL.
     *
     * @param   string  The name of the property
     * @param   mixed   The default value
     * @return  mixed   The value of the property, an associative array or NULL
     */
    public function get($property = null, $default = null)
    {
        $result = $default;

        if(is_null($property)) {
            $result = $this->_state->getData();
        }
        else
        {
            if(isset($this->_state->$property)) {
                $result = $this->_state->$property;
            }
        }

        return $result;
    }

    /**
     * Reset all cached data and reset the model state to it's default
     *
     * @param   boolean If TRUE use defaults when resetting. Default is TRUE
     * @return KModelAbstract
     */
    public function reset($default = true)
    {
        $this->_list  = null;
        $this->_item  = null;
        $this->_total = null;

        $this->_state->reset($default);

        return $this;
    }

    /**
     * Method to get state object
     *
     * @return  object  The state object
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * Method to get a item
     *
     * @return  object
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get a list of items
     *
     * @return  object
     */
    public function getList()
    {
        return $this->_list;
    }

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function getTotal()
    {
        return $this->_total;
    }

	/**
     * Get the model data
     *
     * If the model state is unique this function will call getItem(), otherwise
     * it will calle getList().
     *
     * @return KDatabaseRowset or KDatabaseRow
     */
    public function getData()
    {
        if($this->_state->isUnique()) {
            $data = $this->getItem();
        } else {
            $data = $this->getList();
        }

        return $data;
    }

	/**
     * Get a model state by name
     *
     * @param   string  The key name.
     * @return  string  The corresponding value.
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set a model state by name
     *
     * @param   string  The key name.
     * @param   mixed   The value for the key
     * @return  void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
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
        if(isset($this->_state->$method)) {
            return $this->set($method, $args[0]);
        }

        return parent::__call($method, $args);
    }

	/**
     * Preform a deep clone of the object.
     *
     * @retun void
     */
    public function __clone()
    {
        parent::__clone();

        $this->_state = clone $this->_state;
    }
}