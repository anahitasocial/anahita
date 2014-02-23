<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Relationship
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * A Query clause is a paranthesized condition within a query. The classes is used through a parent query
 * to create independent sub-condition
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Relationship
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainQueryClause extends KObject implements IteratorAggregate, Countable
{
	/**
	 * Parent Query
	 * 
	 * @var AnDomainQuery
	 */
	protected $_parent_query;
		
	/**
	 * Condition
	 * 
	 * @var string 
	 */
	protected $_condition;
		
	/**
	 * Internal Query
	 * 
	 * @var AnDomainQuery
	 */
	protected $_internal_query;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(AnDomainQuery $parent, $condition = 'AND')
	{
		$this->_parent_query   = $parent;
		$this->_condition	   = $condition;
		$this->_internal_query = $parent->getRepository()->getQuery();
		$this->_internal_query->link =& $this->_parent_query->link;		
	}

	/**
	 * @see AnDomainQuery::where
	 * 
	 * @return AnDomainQueryClause
	 */
	public function where( $key, $constraint = null, $value = null , $condition = 'AND' )
	{
		$handle = $this->getHandle();
		
		$this->_internal_query->where($key, $constraint, $value, $condition);
		
		if ( !isset($this->_parent_query->where[$handle]) ) {
			$this->_parent_query->where[$handle] = array('clause'=>$this, 'condition'=>$this->_condition);
		}
						
		return $this;
	}
	
	/**
	 * Return the parent query 
	 *
	 * @return AnDomainQuery
	 */
	public function getParent()
	{
	    return $this->_parent_query;
	}
	
	/**
	 * Returns the count of subclauses
	 * 
	 * @return int
	 */
	public function count()
	{
		return count($this->_internal_query->where);
	}
	
	/**
	 * Allows iterator over the where condition
	 * 
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() 
	{
		return new ArrayIterator($this->_internal_query->where);
	}
	
	/**
	 * Passthrough the bind
	 * 
	 * @param string $key   Bind key
	 * @param string $value Bind value
	 * 
	 * @return AnDomainQueryClause
	 */
	public function bind($key, $value = null)
	{
		$this->_parent_query->bind($key, $value);
		return $this;
	}
	
	/**
	 * Manually ends the clause. This is useful for when using in chain method calls
	 * For example
	 * $query->where($condition1)
	 *  ->clause()
	 * 	->where($subcondition)
	 *  ->end()
	 *  ->where($condition2)
	 * 
	 */
	public function end()
	{
		return $this->_parent_query;
	}
	
	/**
	 * If the $method is one of the MySQL
	 * 
	 * @see KObject::__call()
	 */
    public function __call($method, $arguments)
    {
		if ( isset($arguments[0]) && $this->_parent_query->getRepository()->getDescription()->getProperty($method) )
    	{
    		 $condition = isset($arguments[1]) ? $arguments[1] : 'AND'; 
    		 $this->where($method,'=',$arguments[0], $condition);
    		 return $this;  			
    	}

    	return parent::__call($method, $arguments);
    }	
}