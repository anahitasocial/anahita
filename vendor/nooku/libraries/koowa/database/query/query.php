<?php
/**
 * @version		$Id: query.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Database
 * @subpackage  Query
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Database Select Class for database select statement generation
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Query
 */
class KDatabaseQuery
{
	/**
	 * Count operation
	 *
	 * @var boolean
	 */
	public $count	  = false;

	/**
	 * Distinct operation
	 *
	 * @var boolean
	 */
	public $distinct  = false;

	/**
	 * The columns
	 *
	 * @var array
	 */
	public $columns = array();

	/**
	 * The from element
	 *
	 * @var array
	 */
	public $from = array();

	/**
	 * The join element
	 *
	 * @var array
	 */
	public $join = array();

	/**
	 * The where element
	 *
	 * @var array
	 */
	public $where = array();

	/**
	 * The group element
	 *
	 * @var array
	 */
	public $group = array();

	/**
	 * The having element
	 *
	 * @var array
	 */
	public $having = array();

	/**
	 * The order element
	 *
	 * @var string
	 */
	public $order = array();

	/**
	 * The limit element
	 *
	 * @var integer
	 */
	public $limit = null;

	/**
	 * The limit offset element
	 *
	 * @var integer
	 */
	public $offset = null;

	/**
	 * Database connector
	 *
	 * @var		object
	 */
	protected $_adapter;

	/**
	 * Table prefix
	 *
	 * @var		object
	 */
	protected $_prefix;

	/**
	 * Object constructor
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct( KConfig $config )
	{
        //If no config is passed create it
		if(!isset($config)) $config = new KConfig();

	    //Initialise the object
        $this->_initialize($config);

		//set the model adapter
		$this->_adapter = $config->adapter;
	}


    /**
     * Initializes the options for the object
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
            'adapter' => '',
        ));
    }

    /**
     * Gets the database adapter for this particular KDatabaseQuery object.
     *
     * @return KDatabaseAdapterInterface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

	/**
     * Set the database adapter for this particular KDatabaseQuery object.
     *
     * @param object A KDatabaseAdapterInterface object
     * @return KDatabaseQuery
     */
    public function setAdapter(KDatabaseAdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Built a select query
     *
     * @param   array|string    A string or an array of column names
     * @return  KDatabaseQuery
     */
    public function select( $columns = '*')
    {
        settype($columns, 'array');

        $this->columns = array_unique( array_merge( $this->columns, $columns ) );
        return $this;
    }

    /**
     * Built a count query
     *
     * @return KDatabaseQuery
     */
    public function count()
    {
        $this->count   = true;
        $this->columns = array();
        return $this;
    }

    /**
     * Make the query distinct
     *
     * @return KDatabaseQuery
     */
    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Built the from clause of the query
     *
     * @param   array|string    A string or array of table names
     * @return  KDatabaseQuery
     */
    public function from( $tables )
    {
        settype($tables, 'array');

        //The table needle
        $needle = $this->_adapter->getTableNeedle();

        //Prepent the table prefix
        foreach($tables as &$table)
        {
            if(strpos($table, $needle) !== 0) {
                $table = $needle.$table;
            }
        }

        $this->from = array_unique(array_merge($this->from, $tables));
        return $this;
    }

    /**
     * Built the join clause of the query
     *
     * @param string        The type of join; empty for a plain JOIN, or "LEFT", "INNER", etc.
     * @param string        The table name to join to.
     * @param string|array  Join on this condition.
     * @return KDatabaseQuery
     */
    public function join($type, $table, $condition)
    {
        settype($condition, 'array');

        //The table needle
        $needle = $this->_adapter->getTableNeedle();

        if(strpos($table, $needle) !== 0) {
            $table = $needle.$table;
        }

        $this->join[] = array(
            'type'      => strtoupper($type),
            'table'     => $table,
            'condition' => $condition,
        );

        return $this;
    }

    /**
     * Built the where clause of the query
     *
     * @param   string          The name of the property the constraint applies too, or a SQL function or statement
     * @param   string          The comparison used for the constraint
     * @param   string|array    The value compared to the property value using the constraint
     * @param   string          The where condition, defaults to 'AND'
     * @return  KDatabaseQuery
     */
    public function where( $property, $constraint = null, $value = null, $condition = 'AND' )
    {
        if(!empty($property))
        {
            $where = array();
            $where['property'] = $property;

            if(isset($constraint))
            {
                $constraint = strtoupper($constraint);
                $condition  = strtoupper($condition);

                $where['constraint'] = $constraint;
                $where['value']      = $value;
            }

            $where['condition']  = count($this->where) ? $condition : '';

            //Make sure we don't store the same where clauses twice
            $signature = md5($property.$constraint.$value);
            if(!isset($this->where[$signature])) {
                $this->where[$signature] = $where;
            }
        }

        return $this;
    }

    /**
     * Built the group clause of the query
     *
     * @param   array|string    A string or array of ordering columns
     * @return  KDatabaseQuery
     */
    public function group( $columns )
    {
        settype($columns, 'array'); //force to an array

        $this->group = array_unique( array_merge( $this->group, $columns));
        return $this;
    }

    /**
     * Built the having clause of the query
     *
     * @param   array|string    A string or array of ordering columns
     * @return  KDatabaseQuery
     */
    public function having( $columns )
    {
        settype($columns, 'array'); //force to an array

        $this->having = array_unique( array_merge( $this->having, $columns ));
        return $this;
    }

    /**
     * Build the order clause of the query
     *
     * @param   array|string  A string or array of ordering columns
     * @param   string        Either DESC or ASC
     * @return  KDatabaseQuery
     */
    public function order( $columns, $direction = 'ASC' )
    {
        settype($columns, 'array'); //force to an array

        foreach($columns as $column)
        {
            $this->order[] = array(
                'column'    => $column,
                'direction' => $direction
            );
        }

        return $this;
    }

    /**
     * Built the limit element of the query
     *
     * @param   integer Number of items to fetch.
     * @param   integer Offset to start fetching at.
     * @return  KDatabaseQuery
     */
    public function limit( $limit, $offset = 0 )
    {
        $this->limit  = (int) $limit;
        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * Render the query to a string
     *
     * @return  string  The completed query
     */
    public function __toString()
    {
        $query = '';
        if(!empty($this->columns) || $this->count)
        {
            $query = 'SELECT';

            if($this->distinct) {
                $query .= ' DISTINCT';
            }

            if($this->count) {
                $query .= ' COUNT(*)';
            }
        }

        if (!empty($this->columns) && ! $this->count)
        {
            $columns = array();
            foreach($this->columns as $column) {
                $columns[] = $this->_adapter->quoteName($column);
            }

            $query .= ' '.implode(' , ', $columns);
        }

        if (!empty($this->from))
        {
            $tables = array();
            foreach($this->from as $table) {
                $tables[] = $this->_adapter->quoteName($table);
            }

            $query .= ' FROM '.implode(' , ', $tables);
        }

        if (!empty($this->join))
        {
            $joins = array();
            foreach ($this->join as $join)
            {
                $tmp = ' ';

                if (! empty($join['type'])) {
                    $tmp .= $join['type'] . ' ';
                }

                $tmp .= ' JOIN ' . $this->_adapter->quoteName($join['table']);
                $tmp .= ' ON (' . implode(' AND ', $this->_adapter->quoteName($join['condition'])) . ')';

                $joins[] = $tmp;
            }

            $query .= implode(' ', $joins);
        }

        if (!empty($this->where))
        {
            $query .= ' WHERE';

            foreach($this->where as $where)
            {
                if(isset($where['condition'])) {
                    $query .= ' '.$where['condition'];
                }

                $query .= ' '. $this->_adapter->quoteName($where['property']);

                if(isset($where['constraint']))
                {
                    $value = $this->_adapter->quoteValue($where['value']);

                    if(in_array($where['constraint'], array('IN', 'NOT IN'))) {
                        $value = ' ( '.$value. ' ) ';
                    }

                    $query .= ' '.$where['constraint'].' '.$value;
                }
            }
        }

        if (!empty($this->group))
        {
            $columns = array();
            foreach($this->group as $column) {
                $columns[] = $this->_adapter->quoteName($column);
            }

            $query .= ' GROUP BY '.implode(' , ', $columns);
        }

        if (!empty($this->having))
        {
            $columns = array();
            foreach($this->having as $column) {
                $columns[] = $this->_adapter->quoteName($column);
            }

            $query .= ' HAVING '.implode(' , ', $columns);
        }

        if (!empty($this->order) )
        {
            $query .= ' ORDER BY ';

            $list = array();
            foreach ($this->order as $order) {
                $list[] = $this->_adapter->quoteName($order['column']).' '.$order['direction'];
            }

            $query .= implode(' , ', $list);
        }

        if (!empty($this->limit)) {
            $query .= ' LIMIT '.$this->offset.' , '.$this->limit;
        }

        return $query;
    }
}