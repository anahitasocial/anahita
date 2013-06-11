<?php
/**
 * @version		$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Row
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract Row Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Row
 */
abstract class KDatabaseRowAbstract extends KObjectArray implements KDatabaseRowInterface
{
    /**
     * Tracks columns where data has been updated. Allows more specific
     * save operations.
     *
     * @var array
     */
    protected $_modified = array();

    /**
     * Tracks the status the row
     *
     * Available row status values are defined as STATUS_ constants in KDatabase
     *
     * @var string
     * @see KDatabase
     */
    protected $_status = null;

    /**
     * The status message
     *
     * @var string
     */
    protected $_status_message = '';

    /**
     * Tracks if row data is new
     *
     * @var bool
     */
    protected $_new = true;

    /**
	 * Name of the identity column in the rowset
	 *
	 * @var	string
	 */
	protected $_identity_column;

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();

        parent::__construct($config);

        // Set the table indentifier
    	if(isset($config->identity_column)) {
			$this->_identity_column = $config->identity_column;
		}

        // Reset the row
        $this->reset();

        // Set the new state of the row
        $this->_new = $config->new;

        // Set the row data
        if(isset($config->data))  {
            $this->setData((array) KConfig::unbox($config->data), $this->_new);
        }

        //Set the status
        if(isset($config->status)) {
            $this->setStatus($config->status);
        }

        //Set the status message
        if(!empty($config->status_message)) {
            $this->setStatusMessage($config->status_message);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
             'data'             => null,
             'new'              => true,
             'status'           => null,
             'status_message'   => '',
             'identity_column'  => null
        ));

        parent::_initialize($config);
    }

	/**
	 * Test the connected status of the row.
	 *
	 * @return	boolean	Returns TRUE by default.
	 */
    public function isConnected()
	{
	    return true;
	}

 	/**
    * Returns an associative array of the raw data
    *
    * @param   boolean  If TRUE, only return the modified data. Default FALSE
    * @return  array
    */
    public function getData($modified = false)
    {
        if($modified) {
            $result = array_intersect_key($this->_data, $this->_modified);
        } else {
            $result = $this->_data;
        }

        return $result;
    }

    /**
     * Set the row data
     *
     * @param   mixed   Either and associative array, an object or a KDatabaseRow
     * @param   boolean If TRUE, update the modified information for each column being set.
     *                  Default TRUE
     * @return  KDatabaseRowAbstract
     */
     public function setData( $data, $modified = true )
     {
        if($data instanceof KDatabaseRowInterface) {
            $data = $data->toArray();
        } else {
            $data = (array) $data;
        }

        if($modified)
        {
            foreach($data as $column => $value) {
                $this->$column = $value;
            }
        }
        else
        {
            $this->_data = array_merge($this->_data, $data);
        }

        return $this;
    }

    /**
     * Returns the status
     *
     * @return string The status
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Set the status
     *
     * @param   string|null     The status value or NULL to reset the status
     * @return  KDatabaseRowAbstract
     */
    public function setStatus($status)
    {
        $this->_status   = $status;
        $this->_new      = false;

        if($status != KDatabase::STATUS_FAILED) {
            $this->_modified = array();
        }

        if($status == KDatabase::STATUS_DELETED) {
            $this->_new = true;
        }

        return $this;
    }

    /**
     * Returns the status message
     *
     * @return string The status message
     */
    public function getStatusMessage()
    {
        return $this->_status_message;
    }


    /**
     * Set the status message
     *
     * @param   string      The status message
     * @return  KDatabaseRowAbstract
     */
    public function setStatusMessage($message)
    {
        $this->_status_message = $message;
        return $this;
    }

    /**
     * Load the row from the database.
     *
     * @return object	If successfull returns the row object, otherwise NULL
     */
    public function load()
    {
        $this->_modified = array();

        return $this;
    }

    /**
     * Saves the row to the database.
     *
     * This performs an intelligent insert/update and reloads the properties
     * with fresh data from the table on success.
     *
     * @return boolean  If successfull return TRUE, otherwise FALSE
     */
    public function save()
    {
        $this->_modified = array();

        return false;
    }

    /**
     * Deletes the row form the database.
     *
     * @return boolean  If successfull return TRUE, otherwise FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Resets to the default properties
     *
     * @return boolean  If successfull return TRUE, otherwise FALSE
     */
    public function reset()
    {
        $this->_data     = array();
        $this->_modified = array();

        return true;
    }

    /**
     * Count the rows in the database based on the data in the row
     *
     * @return integer
     */
    public function count()
    {
        return false;
    }

    /**
     * Set row field value
     *
     * If the value is the same as the current value and the row is loaded from the database
     * the value will not be reset. If the row is new the value will be (re)set and marked
     * as modified
     *
     * @param   string  The column name.
     * @param   mixed   The value for the property.
     * @return  void
     */
    public function __set($column, $value)
    {
        if(!isset($this->_data[$column]) || ($this->_data[$column] != $value) || $this->isNew())
        {
            parent::__set($column, $value);

            $this->_modified[$column] = true;
        }
    }

    /**
     * Unset a row field
     *
     * @param   string  The column name.
     * @return  void
     */
    public function __unset($column)
    {
         parent::__unset($column);

         unset($this->_modified[$column]);
    }

 	/**
     * Gets the identitiy column of the rowset
     *
     * @return string
     */
    public function getIdentityColumn()
    {
        return $this->_identity_column;
    }

    /**
     * Get a list of columns that have been modified
     *
     * @return array    An array of column names that have been modified
     */
    public function getModified()
    {
        return array_keys($this->_modified);
    }

    /**
     * Check if a column has been modified
     *
     * @param   string  The column name.
     * @return  boolean
     */
    public function isModified($column)
    {
        $result = false;
        if(isset($this->_modified[$column]) && $this->_modified[$column]) {
            $result = true;
        }

        return $result;
    }

    /**
     * Checks if the row is new or not
     *
     * @return bool
     */
    public function isNew()
    {
        return (bool) $this->_new;
    }

	/**
	 * Search the mixin method map and call the method or trigger an error
	 *
	 * Function is also capable of checking is a behavior has been mixed succesfully
	 * using is[Behavior] function. If the behavior exists the function will return
	 * TRUE, otherwise FALSE.
	 *
	 * @param  string 	The function name
	 * @param  array  	The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
	 */
	public function __call($method, $arguments)
	{
		// If the method is of the form is[Bahavior] handle it.
		$parts = KInflector::explode($method);

		if($parts[0] == 'is' && isset($parts[1]))
		{
			if(isset($this->_mixed_methods[$method])) {
				return true;
			}

			return false;
		}

		return parent::__call($method, $arguments);
	}
}
