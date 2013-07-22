<?php
/**
 * @version		$Id: abstract.php 4648 2012-05-13 21:47:06Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Rowset
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract Rowset Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Rowset
 * @uses 		KMixinClass
 */
abstract class KDatabaseRowsetAbstract extends KObjectSet implements KDatabaseRowsetInterface
{
    /**
	 * Name of the identity column in the rowset
	 *
	 * @var	string
	 */
	protected $_identity_column;
	
	/**
	* Clone row object when adding data
	*
	* @var	boolean
	*/
	protected $_row_cloning;

    /**
     * Constructor
     *
     * @param KConfig|null $config  An optional KConfig object with configuration options
     * @return \KDatabaseRowsetAbstract
     */
    public function __construct(KConfig $config = null)
    {
  		//If no config is passed create it
		if(!isset($config)) $config = new KConfig();
    	
    	parent::__construct($config);
    	
    	$this->_row_cloning = $config->row_cloning;
    		
    	// Set the table indentifier
    	if(isset($config->identity_column)) {
			$this->_identity_column = $config->identity_column;
		}
		
		// Reset the rowset
		$this->reset();
	
		// Insert the data, if exists
		if(!empty($config->data)) {
			$this->addData($config->data->toArray(), $config->new);	
		}
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $object An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'data'              => null,
            'new'               => true,
            'identity_column'   => null,
            'row_cloning'		=> true
        ));

        parent::_initialize($config);
    }
    
	/** 
	 * Test the connected status of the rowset.
	 *
	 * @return	bool	Returns TRUE by default.
	 */
    public function isConnected()
	{
	    return true;
	}
    
	/**
     * Insert a row into the rowset
     * 
     * The row will be stored by it's identity_column if set or otherwise by
     * it's object handle.
     *
     * @param  KDatabaseRowInterface $row
     * @return boolean	TRUE on success FALSE on failure
     * @throws InvalidArgumentException if the object doesn't implement KDatabaseRowInterface
     */
    public function insert(KObjectHandlable $row)
    {
        if(!$row instanceof KDatabaseRowInterface) {
            throw new InvalidArgumentException('Row needs to implement KDatabaseRowInterface');
        }

        if(isset($this->_identity_column)) {
            $handle = $row->{$this->_identity_column};
        } else {
            $handle = $row->getHandle();
        }
        
        if($handle) {
            $this->_object_set->offsetSet($handle, $row);
        }
        
        return true;
    }

 	/**
     * Removes a row from the rowset
     *
     * The row will be removed based on it's identity_column if set or otherwise by
     * it's object handle.
     *
     * @param  KDatabaseRowInterface $row
     * @return \KDatabaseRowsetAbstract
     * @throws InvalidArgumentException if the object doesn't implement KDatabaseRowInterface
     */
    public function extract(KObjectHandlable $row)
    {
        if(!$row instanceof KDatabaseRowInterface) {
            throw new InvalidArgumentException('Row needs to implement KDatabaseRowInterface');
        }

        if(isset($this->_identity_column)) {
           $handle = $row->{$this->_identity_column};
        } else {
           $handle = $row->getHandle();
        }
        
        if($this->_object_set->offsetExists($handle)) {
           $this->_object_set->offsetUnset($handle);
        }
    
        return $this;
    }
 
    /**
     * Returns all data as an array.
     *
     * @param  bool  $modified  If TRUE, only return the modified data. Default FALSE
     * @return array
     */
    public function getData($modified = false)
    {
        $result = array();
        foreach ($this as $key => $row)  {
            $result[$key] = $row->getData($modified);
        }
        return $result;
    }
    
    /**
     * Set the rowset data based on a named array/hash
     *
     * @param   mixed   $data     Either and associative array, a KDatabaseRow object or object
     * @param   boolean $modified If TRUE, update the modified information for each column being set. Default TRUE
     * @return  \KDatabaseRowsetAbstract
     */
     public function setData( $data, $modified = true )
     { 
        //Prevent changing the identity column
        if(isset($this->_identity_column)) {
            unset($data[$this->_identity_column]);
        }

        //Set the data in the rows
        if($modified)
        {
            foreach($data as $column => $value) {
                $this->setColumn($column, $value);
            }
        }
        else
        {
            foreach ($this as $row) {
                $row->setData($data, false);
            }
        }

        return $this;
    }
    
	/**
     * Add rows to the rowset
     * 
     * This function will either clone the row object, or create a new instance of the row object for 
     * each row being inserted. By default the row will be cloned. 
     *
     * @param  array $rows An associative array of row data to be inserted.
     * @param  bool  $new  If TRUE, mark the row(s) as new (i.e. not in the database yet). Default TRUE
     * @return  \KDatabaseRowsetAbstract
     * @see __construct
     */
    public function addData(array $rows, $new = true)
    {   
        $options = array(
            'status' => $new ? NULL : KDatabase::STATUS_LOADED,
            'new'    => $new,   
        );
        
        if($this->_row_cloning)
        {
            $default = $this->getRow($options);
         
            foreach($rows as $k => $data)
            {
                $row = clone $default;  
                $row->setData($data, $new);
            
                $this->insert($row);
            }
        }
        else 
        {
            foreach($rows as $k => $data)
            {
                $options['data'] = $data;
                
                $row = $this->getRow($options);
                $this->insert($row);
            }
        }
        
        return $this;
    }
    
	/**
     * Retrieve an array of column values
     *
     * @param   string  $column The column name.
     * @return  array   An array of all the column values
     */
    public function getColumn($column)
    {
        $result = array();
        foreach($this as $key => $row) {
            $result[$key] = $row->$column;        
        }

        return $result;
    }

    /**
     * Set the value of all the columns
     *
     * @param   string  $column The column name.
     * @param   mixed   $value The value for the property.
     * @return  void
     */
    public function setColumn($column, $value)
    {
        //Set the data
        foreach($this as $row) {
            $row->$column = $value;
        }
    }
   
    /**
     * Gets the identity column of the rowset
     *
     * @return string
     */
    public function getIdentityColumn()
    {
        return $this->_identity_column;
    }

    /**
     * Returns a KDatabaseRow 
     * 
     * This functions accepts either a know position or associative array of key/value pairs
     *
     * @param   string|array  $needle The position or the key or an associative array of column data to match
     * @return KDatabaseRow(set)Abstract Returns a row or rowset if successful. Otherwise NULL.
     */
    public function find($needle)
    {
        $result = null;
        
        if(!is_scalar($needle))
        {
            $result = clone $this;
            
            foreach ($this as $i => $row) 
            { 
                foreach($needle as $key => $value)
                {
                    if(!in_array($row->{$key}, (array) $value)) {
                        $result->extract($row);
                    } 
                }
            }
        }
        else 
        {
            if(isset($this->_object_set[$needle])) {
                $result = $this->_object_set[$needle];
            }
        }

        return $result;
    }

    /**
     * Saves all rows in the rowset to the database
     *
     * @return boolean  If successful return TRUE, otherwise FALSE
     */
    public function save()
    {
        $result = false;
        
        if(count($this))
        {
            $result = true;
           
            foreach ($this as $i => $row) 
            {
                if(!$row->save()) {
                    $result = false;
                }
            }
        } 
        
        return $result;
    }

    /**
     * Deletes all rows in the rowset from the database
     *
     * @return bool  If successful return TRUE, otherwise FALSE
     */
    public function delete()
    {
        $result = false;
        
        if(count($this))
        {
            $result = true;
           
            foreach ($this as $i => $row) 
            {
                if(!$row->delete()) {
                    $result = false;
                }
            }
        } 
       
        return $result;
    }

    /**
     * Reset the rowset
     *
     * @return bool  If successful return TRUE, otherwise FALSE
     */
    public function reset()
    {
        $this->_object_set->exchangeArray(array());

        return true;
    }
    
	/**
     * Get an instance of a row object for this rowset
     *
     * @param	array $options An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function getRow(array $options = array())
    { 
        $identifier         = clone $this->getIdentifier();
        $identifier->path   = array('database', 'row');
        $identifier->name   = KInflector::singularize($this->getIdentifier()->name);
            
        //The row default options
        $options['identity_column'] = $this->getIdentityColumn();
               
        return $this->getService($identifier, $options); 
    }
         
	/**
     * Return an associative array of the data.
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this as $key => $row)  {
            $result[$key] = $row->toArray();
        }
        return $result;
    }
    
    /**
     * Search the mixin method map and call the method or forward the call to each row for processing.
     * 
     * Function is also capable of checking is a behavior has been mixed successful using is[Behavior] function. If the
     * behavior exists the function will return TRUE, otherwise FALSE.
     *
     * @param  string   $method    The function name
     * @param  array    $arguments The function arguments
     * @throws BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        $parts = KInflector::explode($method);

        //If the method is of the form is[Bahavior] handle it
        if($parts[0] == 'is' && isset($parts[1]))
        {
            if(isset($this->_mixed_methods[$method])) {
                return true;
            }

            return false;
        }
        else
        {
             //If the mixed method exists call it for all the rows
            if(isset($this->_mixed_methods[$method]))
            {
                foreach ($this as $i => $row) {
                     $row->__call($method, $arguments);
                }
                
                return $this;
            }
        }

        throw new BadMethodCallException('Call to undefined method :'.$method);
    }
}