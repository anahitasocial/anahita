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
 * Entity data container. It contains all the storable data of an entity. It also
 * handle materializing the data on demand
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainEntityData extends KObject implements ArrayAccess
{
	/**
	 * Synchornization locks
	 * 
	 * @var array
	 */
	static protected $_locks = array();
	
	/**
	 * Set a lock for a repository
	 * 
	 * @param  KObject $object
	 * @param  boolean $lock
	 * @return void
	 */
	protected function _lock($object, $lock)
	{
		if ( $lock ) {
			self::$_locks[$object->getHandle()] = true;
		} else
			unset(self::$_locks[$object->getHandle()]);
	}
	
	/**
	 * Return if a repository is locked
	 * 
	 * @param  KObject $object
	 * @return boolean
	 */
	protected function _isLocked($object)
	{
		return isset(self::$_locks[$object->getHandle()]);
	}
		
	/**
	 * Domain Description
	 * 
	 * @var AnDomainDescriptionAbstract
	 */
	protected $_description;
		
	/**
	 * Domain Entity
	 * 
	 * @var AnDomainEntityAbstract
	 */
	protected $_entity;
	
	/**
	 * Columns(row) data
	 * 
	 * @var array
	 */
	protected $_row  = array();
	
	/**
	 * Property Data
	 * 
	 * @var array
	 */
	protected $_property = array();
	
	/**
	 * An array of properties that have already been materialized
	 *
	 * @var array
	 */
	protected $_materialized = array();
	
	 /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
    	$this->_entity 	    = $config->entity;
    	$this->_description = $config->entity->getEntityDescription();
    	$this->_property	= array();
    }
    
    /**
     * Set the row data. 
     *
     * @param array $data Set the row data
     * 
     * @return void
     */
    public function setRowData(array $data)
    {
    	$this->_row = array_merge($data, $this->_row);
    }
    
    /**
     * Reloads an array of properies
     *
     * @param  array $properties
     * @return void
     */
    public function load($properties)
    {		
        $keys = $this->_entity->getIdentifyingData();
                
		$this->_entity->getRepository()->getCommandChain()->disable();
		 
		$query = $this->_entity->getRepository()->getQuery()
			        ->columns($properties)
		            ->where($keys);
		
		$data  = $this->_entity->getRepository()->fetch($query, AnDomain::FETCH_ROW);

		if ( !empty($data) ) 
		{
		    $this->_row = array_merge($this->_row, (array)$data);
		    
		    foreach($properties as $property) {
		        unset($this->_materialized[$property]);
		    }		    
		}

		$this->_entity->getRepository()->getCommandChain()->enable();
		
		return !empty($data);
    }
    
    /**
     * Get the row data
     *
     * @return array
     */
    public function getRowData()
    {
    	return $this->_row;
    }    
        
 	/**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param   string  The offset
     * @return  bool
     */
    public function offsetExists($key)
    {
       $this->_materialize($key);
       return isset($this->_property[$key]);
    }

    /**
     * Return whether a key has been materialized
     * 
     * @param string $key
     * 
     * @return boolean
     */
    public function isMaterialized($key)
    {
        return isset($this->_materialized[$key]);
    }
    
    /**
     * Get an item from the array by offset
     *
     * Required by interface ArrayAccess
     *
     * @param   string     The offset
     * @return  mixed      The item from the array
     */
    public function offsetGet($key)
    {
    	$this->_materialize($key);
    	
        $result = null;
        if ( isset($this->_property[$key]) )
        	$result = $this->_property[$key];
        	
        return $result;
    }

    /**
     * Set an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param   string     The offset of the item
     * @param   mixed   The item's value
     * @return  object  KObjectSet
     */
    public function offsetSet($key, $value)
    {
    	$this->_setPropertyValue($key, $value);
        return $this; 
    }

    /**
     * Unset an item in the array
     *
     * All numerical array keys will be modified to start counting from zero while
     * literal keys won't be touched.
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset of the item
     * @return  object 	KObjectSet
     */
    public function offsetUnset($key)
    {    	
        unset($this->_property[$key]);
        return $this;
    }              	

    /**
     * Return an array of properties
     * 
     * @return array
     */
    public function toArray()
    {
    	return $this->_property;
    }
    
    /**
     * Materialize a proeprty value before accesing
     *
     * @param string $key
     * @return void
     */
    protected function _materialize($key)
    {
    	if ( empty($this->_row) ) 
    	{
    		//no data has been set, the entity is property 
    		//in the new state lets set the default value accordinly 
    		return;
    	}
			
    	$property = $this->_description->getProperty($key);
    	
    	if ( isset($this->_materialized[$property->getName()]) ) {
    		return;
    	}
    		
    	$repository  = $this->_entity->getRepository();
    	
    	//if a property is serialzable but not materizable
    	//then the data must be missing
    	if ( $property->isSerializable() &&
    	        !$property->isMaterializable($this->_row)
    	)
    	{
    	    //lazy load the value alogn with all the entities whose
    	    //$key value is missing
    	    $repository->getCommandChain()->disable();    	    
    	    $entities = $repository->getEntities();
    	    $query    = $repository->getQuery();
    	    $keys     = array();
    	    foreach($entities as $entity)
    	    {
    	        if ( $entity->persisted() )
    	        {
    	            $data = $entity->getIdentifyingData();
    	            $key   = current(array_keys($data));
    	            $value = current($data);
    	            $keys[$key][] = $value;
    	        }
    	    }
    	
    	    foreach($keys as $key => $values) {
    	        $query->where($key,'IN',$values,'OR');
    	    }
    	    	
    	    $result = $repository->fetch($query, AnDomain::FETCH_ROW_LIST);
    	
    	    foreach($result as $data)
    	    {
    	        $keys   = $repository->getDescription()->getIdentifyingValues($data);
    	        $entity = $repository->find($keys, false);
    	        if ( $entity ) {
    	            $entity->setRowData($data);
    	        }
    	    }
    	
    	    $repository->getCommandChain()->enable();    	    
    	}
    	
    	$value	= $property->materialize($this->_row, $this->_entity);
    		
    	$this->_setPropertyValue($property->getName(), $value);
    	 
    	//when materilize a proxy property
    	//materilize the same proeprty in all the entities
    	//of the repository to allow for lazy loading
    	if ( $property->isRelationship() )
    	    if ( $property->isOneToOne() || $property->isManyToOne() )
    	    {
    	        //prevents calling the block multiple time
    	        //as it tries to instantiate the same property for
    	        //all the entities
    	        if ( !self::_isLocked($repository) ) {
    	    		    		self::_lock($repository, true);
    	    		    		$entities = $repository->getEntities();
    	    		    		foreach($entities as $entity) {
    	    		    		    $entity->get($key);
    	    		    		}
    	    		    		self::_lock($repository, false);
    	        }
    	    }
    }
    
    /**
     * Set a property value for a key.If value is null and the $property is required then
     * set the default value
     *
     * @param string $property Property name
     * @param mixed  $value    Property value
     * 
     * @return void
     */
    protected function _setPropertyValue($property, $value)
    {
        //if a value is null and it's requires set it to the default
    	//value
    	$property = $this->_description->getProperty($property);
    	$name	  = $property->getName();
    	
    	if ( $property->isAttribute() )
    	{
    	    $default = $property->getDefaultValue();
    	    
	    	if ( $value === null && $default ) 
	    	{
				 $value = $default;
	    	}
    	} 
		
		$this->_property[$name] = $value; 
    	$this->_materialized[$name]	= $name;		  	
    }
}