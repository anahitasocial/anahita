<?php
/**
 * @version		$Id: table.php 4648 2012-05-13 21:47:06Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Rowset
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Table Rowset Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Rowset
 * @uses 		KMixinClass
 */
class KDatabaseRowsetTable extends KDatabaseRowsetAbstract
{
	/**
	 * Table object or identifier (com://APP/COMPONENT.table.NAME)
	 *
	 * @var	string|object
	 */
	protected $_table = false;

    /**
     * Constructor
     *
     * @param KConfig|null $config  An optional KConfig object with configuration options
     * @return \KDatabaseRowsetTable
     */
	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		$this->_table = $config->table;

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
			'table'	=> $this->getIdentifier()->name
		));

		parent::_initialize($config);
	}

	/**
     * Method to get a table object
     *
     * Function catches KDatabaseTableExceptions that are thrown for tables that
     * don't exist. If no table object can be created the function will return FALSE.
     *
     * @return \KDatabaseTableAbstract
     */
     public function getTable()
    {
        if($this->_table !== false)
        {
            if(!($this->_table instanceof KDatabaseTableAbstract))
		    {
		        //Make sure we have a table identifier
		        if(!($this->_table instanceof KServiceIdentifier)) {
		            $this->setTable($this->_table);
			    }

		        try {
		            $this->_table = $this->getService($this->_table);
                } catch (KDatabaseTableException $e) {
                    $this->_table = false;
                }
            }
        }

        return $this->_table;
    }

	/**
	 * Method to set a table object attached to the rowset
	 *
	 * @param	mixed	$table  An object that implements KObjectServiceable, KServiceIdentifier object or valid
     *                          identifier string
	 * @throws	KDatabaseRowsetException If the identifier is not a table identifier
	 * @return	\KDatabaseRowsetAbstract
	 */
	public function setTable($table)
	{
		if(!($table instanceof KDatabaseTableAbstract))
		{
			if(is_string($table) && strpos($table, '.') === false )
		    {
		        $identifier         = clone $this->getIdentifier();
		        $identifier->path   = array('database', 'table');
		        $identifier->name   = KInflector::tableize($table);
		    }
		    else  $identifier = $this->getIdentifier($table);

			if($identifier->path[1] != 'table') {
				throw new KDatabaseRowsetException('Identifier: '.$identifier.' is not a table identifier');
			}

			$table = $identifier;
		}

		$this->_table = $table;

		return $this;
	}

	/**
	 * Test the connected status of the row.
	 *
	 * @return	bool	Returns TRUE if we have a reference to a live KDatabaseTableAbstract object.
	 */
    public function isConnected()
	{
	    return (bool) $this->getTable();
	}
	
	/**
     * Add rows to the rowset
     *
     * @param  array  $data  An associative array of row data to be inserted.
     * @param  boole  $new   If TRUE, mark the row(s) as new (i.e. not in the database yet). Default TRUE
     * @return  \KDatabaseRowsetAbstract
     * @see __construct
     */
    public function addData(array $data, $new = true)
    {   
        if($this->isConnected()) {
		    parent::addData($data, $new);
		}
        
        return $this;
    }

	/**
	 * Get an empty row
	 *
	 * @param	array $options An optional associative array of configuration settings.
	 * @return	\KDatabaseRowAbstract
	 */
	public function getRow(array $options = array())
	{
		$result = null;

	    if($this->isConnected()) {
		    $result = $this->getTable()->getRow($options);
		}

	    return $result;
	}

	/**
	 * Forward the call to each row
	 *
	 * This functions overloads KDatabaseRowsetAbstract::__call and implements
	 * a just in time mixin strategy. Available table behaviors are only mixed
	 * when needed.
	 *
	 * @param  string 	$method    The function name
	 * @param  array  	$arguments The function arguments
	 * @return mixed The result of the function
	 */
	public function __call($method, $arguments)
	{
	    // If the method hasn't been mixed yet, load all the behaviors.
		if($this->isConnected() && !isset($this->_mixed_methods[$method]))
		{
			foreach($this->getTable()->getBehaviors() as $behavior) {
				$this->mixin($behavior);
			}
		}

		return parent::__call($method, $arguments);
	}
}