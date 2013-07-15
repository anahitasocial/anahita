<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Entity Proxy is a proxy object for unloaded entities in the belong to/one-to-one
 * relationships. It uses a lazy loading mechanism to load all the entities at once
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainEntityProxy extends KObjectDecorator implements ArrayAccess
{
	/**
	 * An array of similar uniques
	 * 
	 * @var array 
	 */
	static protected $_values = array();
	
	/**
	 * Entity Identifier
	 * 
	 * @var KServiceIdentifier
	 */
	protected $_identifier;
		
	/**
	 * Unique Property
	 * 
	 * @var string
	 */
	protected $_property;

	/**
	 * Unique Property Value
	 * 
	 * @var mixed
	 */
	protected $_value;
	
	/**
	 * Relationship that created the proxy
	 * 
	 * @var AnDomainRelationshipProperty
	 */
	protected $_relationship;
	
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */
	public function __construct(KConfig $config)
	{	                
	    parent::__construct($config);
	    
		$this->_value 		 = $config->value;
		
		$this->_property	 = $config->property;
				
		$this->_relationship = $config->relationship;
		
		self::$_values[$this->getIdentifier().$this->_property][] = $this->_value;
	}
	
	/**
     * Delays retriveing the object to the last moment by checking the property
     * 
     * @return boolean
	 */
	public function __isset($key)
	{
		if ( !isset($this->_object) && $key == $this->_property )
			return isset($this->_value);
		
		return parent::__isset($key);
	}

	/**
     * Delays retriveing the object to the last moment by checking the property
     * 
     * @return mixed
	 */
	public function __get($key)
	{
		if ( !isset($this->_object) && $key == $this->_property ) 		
			return $this->_value;

		return parent::__get($key);
	}
	
	/**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }
    	
   /**
     * Get an item from the data by offset
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset
     * @return  mixed   The item from the array
     */
    public function offsetGet($offset)
    {   
        return $this->__get($offset);
    }

    /**
     * @see self::__unset
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset of the item
     * @param   mixed   The item's value
     * @return  object  
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
        return $this; 
    }

    /**
     * @see self::__unset
     * 
     * @param   int     The offset of the item
     * @return  object 	
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
        return $this;
    }

	/**
 	 * Get a property value directly from the entity
 	 * 
 	 * @return mixed
	 */
	public function get($key = null, $default = null)
	{
		if ( !isset($this->_object) && $key == $this->_property ) 		
			return $this->_value;
			
		return $this->getObject()->get($key, $default);
	}

	/**
 	 * Set a property value directly 
 	 * 
 	 * @return class instance
	 */
	public function set($key = null, $value = null)
	{
		$this->getObject()->set($key, $value);
		return $this;
	}
	
	/**
	 * Return the object handle
	 *
	 * @return string
	 */
	public function getHandle()
	{
	    return $this->getObject()->getHandle();
	}
		
	/**
	 * Get the proxied entity. Since there could many entities proxied. The getObject method will try to
	 * load all the proxied entities of the same type in order to reduce the number of calls 
	 * to the storage later on 
	 * 
	 * @return AnDomainEntityAbstract
	 */
	public function getObject()
	{
		//security check
		if ( !isset($this->_object) ) 
		{
			$condition 	   = array($this->_property=>$this->_value);
			$repository	   = AnDomain::getRepository($this->getIdentifier());
			
			//check if an entity exiting in the repository with $condition
			if ( $data = $repository->find($condition, false) )
			{
				$this->_object = $data;
				return $this->_object;
			}
			
			//now time to fetch the object from the database
			//but lets grab all the similar entities all together
			$handle		   = $this->getIdentifier().$this->_property;
			$values	       = isset(self::$_values[$handle]) ? self::$_values[$handle] : array();
			
			if ( empty($values) ) {
				return null;
			}
			
			$values = AnHelperArray::unique($values);
			$query  = $repository->getQuery();
			AnDomainQueryHelper::applyFilters($query, $this->_relationship->getQueryFilters());
			$query->where(array($this->_property=>$values));
			$entities = $repository->fetchSet($query);
					
			//the object must have been fetched with the set
			//in the previous line
			//if the object is still not fetched, then the object
			//doesn't exists in the databse
			$this->_object = $repository->find($condition, false);
			
			if ( !$this->_object ) 
			{
				//lets cache the null result to prevent re-fetching
				//the same result
				$query = $repository->getQuery()->where($condition)->limit(1);
				
				if ( $repository->hasBehavior('cachable') ) {
				    $repository->emptyCache($query);
				}
				
				$this->_object = false;
				
				//if it's a required one-to-one relationship
				//then instantaite a new entity if the entity doesn't exists
				if ( $this->_relationship->isOneToOne() )
				{
				    if ( $this->_relationship->isRequired() )
				    {
				        $this->_object = $repository->getEntity(array(
                            'data' => array($this->_property=>$this->_value)
				        ));
				    }
				}
			}
			
			unset(self::$_values[$handle]);
		}
		
		return $this->_object;
	}
	
	/**
	 * Overloaded call function
	 *
	 * @param  string 	The function name
	 * @param  array  	The function arguments
	 * @return mixed The result of the function
	 */
	public function __call($method,  $arguments)
	{
	    $object = $this->getObject();
	    if ( $object ) {
	        return call_object_method($object, $method, $arguments);
	    }
	}
}