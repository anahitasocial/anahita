<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
 * Abstract Domain Entity 
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class AnDomainEntityAbstract extends KObject implements ArrayAccess
{	
	/**
	 * Static cache to store runtime information of an entity
	 * 
	 * @return ArrayObject
	 */
	protected static function _cache($entity)
	{
	    static $cache;
	    
		if ( !$cache ) {
			$cache = new AnObjectArray();
		}
				
    	if ( !isset($cache[$entity->getRepository()]) ) {
    		$cache[$entity->getRepository()] = new ArrayObject();
    	}
    	    	
    	return $cache[$entity->getRepository()];
	}
	
	/**
	 * Stores the properties of the entity that have been modified
	 * 
	 * @var array();
	 */
	protected $_modified = array();
	
	/**
	 * Repository
	 * 
	 * @var AnDomainRepositoryAbstract
	 */
	protected $_repository;
	
	/**
	 * Entity properties
	 * 
	 * @var AnDomainEntityData
	 */
	protected $_data;
	
	/**
	 * Entity error object
	 * 
	 * @var AnDomainEntityException
	 */
	protected $_error;
	
	/**
	 * Flag to determine if an entity has been persisted into the database or not
	 * this flag is only set after an entity has been fetched from the database
	 * 
	 * @var boolean
	 */
	protected $_persisted = false;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
	    //very crucial to first store this instance in the service container
	    //as this instnace will be used to clone other instances	    
	    $config->service_container->set($config->service_identifier, $this);
	    
		parent::__construct($config);

		//set the repository
		$this->_repository = $config->repository;
		
		//set the master (prototype)
		$config->prototype = $this;
		
		$config->append(array(
             'auto_generate' => count(KConfig::unbox($config->attributes)) == 0
		));
		
		$this->getService($config->repository, KConfig::unbox($config));
		
		//if there are no keys
		if ( !count($this->description()->getKeys()) ) 
		{
		    //try to guess the key
		    $this->description()->setAttribute('id', array('key'=>true));
		    
		    if ( !count($this->description()->getKeys()) )
		        throw new AnDomainDescriptionException('Entity '.$this->getIdentifier().' needs at least one key');
		}
	}
	
	/**
	 * Initializes the default configuration for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
	    $identifier       = clone $this->getIdentifier();
	    $identifier->path = array('domain','repository');
	    register_default(array('identifier'=>$identifier, 'prefix'=>$this));
	    $config->append(array(
	        'attributes'        => array(),
	        'relationships'     => array(),
	        'repository'        => $identifier,	            
	        'entity_identifier' => $this->getIdentifier()
	    ));
	    parent::_initialize($config);
	}	
	
	/**
	 * Sets the enity data after it has been fetched from the database storage
	 * 
	 * @param KCommandContext $context Context
	 * 
	 * @return void
	 */
	protected function _afterEntityFetch(KCommandContext $context) 
	{
        //set the persistd flag        
	    $this->setPersisted(true);
	    
	    //set the raw data
		$this->_data->setRowData($context['data']);
				
		//force setting the keys
		foreach($context['keys'] as $key => $value)	
			$this->_data[$key] = $value;
	}
	
	/**
	 * Set an error for the entity
	 *
	 * @param string|KException $error The error message or an error object
	 * @param int               $code  An optional error code
	 *
	 * @return void
	 */
	public function setError($error, $code = null)
	{
	    if ( is_string($error) ) {
	        $error = new AnDomainEntityException($error, $code);
	    }
	
	    $this->_error = $error;
	
	    return $this;
	}
	
	/**
	 * Returns the entity error
	 *
	 * @return KException
	 */
	public function getError()
	{
	    return $this->_error;
	}
		
	/**
	 * Returns the state of the entity. It returns one of the constants
	 * 
	 * @return string
	 */
	final public function state()
	{
		return $this->__space->getState($this);
	}
	
	/**
	 * Return whether the entity is in a valid state
	 * 	 
	 * @param KCommandContext Context parameter. Can be null
	 * 
	 * @return boolean
	 */
	public function validate(KCommandContext $context = null)
	{
		return $this->getRepository()->getSpace()->validate($context);
	}

	/**
	 * Persists the entity into the repository
	 * 
	 * @param KCommandContext Context parameter. Can be null
	 * 
	 * @return boolean
	 */
	public function save(KCommandContext $context = null)
	{		
		return $this->getRepository()->getSpace()->commit($context);
	}

	/**
	 * Set the properties/values have been modified 
	 * 
	 * @return array
	 */
	public function modified()
	{
		return array_keys($this->_modified);
	}
	
	/**
	 * Return an array of modifeid properties with their old and new values
	 * 
	 * @return KConfig
	 */
	public function modifications()
	{
		return new KConfig($this->_modified);
	}
	
	/**
	 * Reset the state of a entity to clean state
	 * 
	 * @return AnDomainEntityAbstract
	 */
	public function reset()
	{
		$this->__space->setState($this, AnDomain::STATE_CLEAN);
		$this->_modified = array();
		return $this;
	}
	
	/**
	 * Set whether an enity is persisted in the database or not
	 *
	 * @param boolean $persisted Persistance flag
	 * 
	 * @return void
	 */
	public function setPersisted($persisted)
	{
	    $this->_persisted = $persisted;
	}
	
	/**
	 * Return if the entity is persisted,  itÕs not a new record and it was not destroyed.
	 * 
	 * @return boolean
	 */
	final public function persisted()
	{	    
		return $this->_persisted;
	}
	
    /**
     * Return the entity identity property name
     * 
     * @return string
     */
    public function getIdentityProperty()
    {
        return $this->description()->getIdentityProperty()->getName();
    }
    
	/**
	 * Returns the value of the entity identity property
	 * 
	 * @return int
	 */
	public function getIdentityId()
	{
		return $this->get($this->getIdentityProperty());
	}
	
	/**
	 * Get the raw value of a property or return the default value passed
	 * 
	 * @param  string $name    Then name of the property
	 * @param  mixed  $default The default value
	 * 
	 * @return mixed
	 */
	 public function get($name = null, $default = null)
	 {
	 	$description = $this->description();
	 	
	 	//get the property
		$property = $description->getProperty($name);
		
		if ( !$property ) {
		    return parent::get($name, $default);
		}
		
		//get the property name
		$name  = $property->getName();
					
		$value = $this->_data->offsetGet($name);

		if ( $property->isRelationship() && $property->isOneToMany() && is_null($value) ) {
			//since it's an external relationship
			//lets instantitate a dummy relationship
			//this should happen for the one-to-one relationships
			if ( $property->isOneToOne() ) {
				return null;
			}
			
			$value = $this->_data[$name] = $property->getSet($this);
			return $value;
		}
		
		return is_null($value) ? $default : $value; 		
	}
		
	/**
	 * Set the raw value of a property
	 * 
	 * @param string $name 
	 * @param mixed $value 
	 * @return void;
	 */
	public function set($name, $value = null)
	{
		$property = $this->description()->getProperty($name);
				
		if ( !$property ) 
			return parent::set($name, $value);
		
		//if a value is mixin then get its mixer
		if ( $value instanceof KMixinAbstract )
			$value = $value->getMixer();
			
		$modify = false;
		$name 	= $property->getName();
				
		$context   = $this->getRepository()->getCommandContext();
        
        $context['property'] = $property;
        $context['value']    = $value;
        $context['entity']   = $this;

        if ( $this->getRepository()->getCommandChain()->run('before.setdata', $context) === false )
            return $this;
				
		$value = $context->value;
				
		if ( $property->isSerializable() ) 
		{
			$modify = true;
			if ( $property->isRelationship() && $property->isManyToOne() && $value )
			{
				if ( !is($value, 'AnDomainEntityAbstract', 'AnDomainEntityProxy') )
					throw new AnDomainExceptionType('Value of '.$property->getName().' must be a AnDomainEntityAbstract');
					
				//if a relationship is a belongs to then make sure the parent
				//is always saved before child
				//example if $topic->author = new author
				//then save the new author first before saving topic
				//only set the dependency 
				if ( $value->state() == AnDomain::STATE_NEW )
					$this->__space->setDependent($this, $value);
			}
			//if value is not null do a composite type checking
			if ( !is_null($value)  )
				if ( $property->isAttribute() && !$property->isScalar() )
				{
					if ( !is($value, $property->getType()) ) {
						throw new AnDomainEntityException('Value of '.$property->getName().' must be a '.$property->getType().'. a '.get_class($value).' is given.');
					}
				}				
		} 
		elseif ( $property->isRelationship() && $property->isOnetoOne() )
		{
			$child	   = $property->getChildProperty();
			$current   = $this->get($property->getName());

			//if there's a current value and it's existence depends on 
			//the parent entity then remove the current
			if ( $current ) 
			{
				if (  $child->isRequired() ) {
					$current->delete();
				}
			}
			
			//if a one-to-one relationship then there must be a child key for the property
			//then must set the inverse			
			if ( $value ) {
				$value->set($child->getName(), $this);
			}
			
			$this->_data[$property->getName()] 	= $value;
		} 
		elseif ( $property->isRelationship() && ($property->isManyToMany() || $property->isOneToMany()) )
		{
            $current = $this->get($name);
            
            if ( $current instanceof AnDomainEntitysetOnetomany )
            {
                $values = KConfig::unbox($value);                
                //can be an KObjectArray or KObjectSet object
                if ( $values instanceof KObject && $values instanceof Iterator )
                {
                    $current->delete();
                    foreach($values as $value)  {
                        $current->insert($value);
                    }
                }
            }
		} 
		
		//only modify if the current value is differnet than the new value
		$modify = $modify && !is_eql($this->get($name), $value);
		
		if ( $modify ) 
		{
			//lets bring them back to their orignal type
			if ( !is_null($value) && $property->isAttribute() && $property->isScalar() ) {
				settype($value, $property->getType());
			}
			
			if ( $this->state() != AnDomain::STATE_NEW )
			{
				//store the original value for future checking
				if ( !isset($this->_modified[$name]) ) 
					$this->_modified[$name] = array('old' => $this->get($name));
					
				$this->_modified[$name]['new'] 		   = $value;
				
				//check if the new value is the same as the old one then remove the 			
				if ( is_eql($this->_modified[$name]['old'], $this->_modified[$name]['new']) ) {
					//if there are no modified then reset the entity
					unset($this->_modified[$name]);
					if ( count($this->_modified) === 0 ) {
						$this->reset();
					}
				}
			}
						
			$this->_data[$property->getName()] 	   = $value;
			$this->__space->setState($this, AnDomain::STATE_MODIFIED);
			
			//only track modifications for the updated entities
			if ( $this->state() !== AnDomain::STATE_MODIFIED) {
				$this->_modified = array();
			}
			

		}
		
		return $this;
	}
	
	/**
	 * ReLoad the entity properties from storage. Overriding any changes
	 * 
	 * @param array $properties An array of properties. If no properties is passed then
	 * all of the properites are loaded
	 * 
	 * @return void
	 */
	public function load($properties = array())
	{
		if ( $this->persisted() )
		{		    
			settype($properties, 'array');
	
			if ( empty($properties) ) 
			{
			    //only load serializbale properties (i.e. attributes, many to one relationships)
				$properties = array();
				foreach($this->description()->getProperty() as $property)
				{
				    if ( $property->isSerializable() )
				        $properties[] = $property->getName();
				}
				$keys		= array_keys($this->description()->getKeys());
				$properties = array_diff($properties, $keys);
			}
			
			$this->_data->load($properties);
			
			//the loaded properties are no longer modified
			foreach($properties as $property) {
				unset($this->_modified[$property]);
			}
			
			//reset the element if there are no modified
			if ( count($this->_modified) === 0 )
				$this->reset();
		}
	}
    
    /**
     * This method is used to return an array of entity row data that have either 
     * been changed or are new
     * 
     * @return  array
     */
    public function getAffectedRowData()
    {
        $data        = array();
        $description = $this->description();
        switch($this->state())
        {
            case AnDomain::STATE_NEW :
                //get all the serializable property/value pairs
                foreach($description->getProperty() as $name => $property ) 
                    if ( $property instanceof AnDomainPropertySerializable)
                        $data[$name] = $name;
                unset($data[$description->getIdentityProperty()->getName()]);
                break;
            case AnDomain::STATE_MODIFIED : 
                //get all the updated serializable property/value pairs
                $data            = $this->modified();
                break;
            case  AnDomain::STATE_DELETED :
                break;
            default :
                return $data;
        }
        
        $tmp = array();
        
        foreach($data as $name) 
        {
            $value    = $this->get($name);
            $property = $description->getProperty($name);
            $tmp      = array_merge($tmp, $property->serialize($value));        
        }
        
        $data = $tmp;
        
        if ( $description->getInheritanceColumn() && $this->state() == AnDomain::STATE_NEW ) {
            $data[(string)$description->getInheritanceColumn()] = (string)$description->getInheritanceColumnValue();
        }
        
        return $data;
    }
        
	/**
	 * Set the row (raw) data of the entity
	 *
	 * @param array $row The row data of an entity
	 * 
	 * @return void
	 */
	public function setRowData(array $row)
	{        
	    $this->_data->setRowData($row);
	}
    
    /**
     * Return an array of row data. If a value is passed for $column then it returns
     * the value of the column wihtin the row
     * 
     * @param string $column The column name. Optional can be null 
     * 
     * @return void
     */
    public function getRowData($column = null)
    {
        $data = $this->_data->getRowData();
        
        if ( !is_null($column) )
            $data = isset($data[$column]) ? $data[$column] : null;
        
        return $data;        
    }
	
	/**
	 * Set the value of a property by checking for custom setter. An array 
	 * can be passed to set multiple properties
	 * 
	 * @param string|array $property Property name 
	 * @param mixd         $value    Property value
	 *  
	 * @return void
	 */	
    public function setData($property, $value = null )
    {  	
    	$property = KConfig::unbox($property);
    	
    	if(is_array($property)) 
    	{
    		$description	= $this->description();
    		$properties	 	= $property;
    		$access  		= pick($value, AnDomain::ACCESS_PUBLIC);        	
        	foreach ($properties as $key => $value)
        	{
        		$property = $description->getProperty($key);
            	if ( $property && $property->getWriteAccess() >= $access ) 
            	{
            		//ignore any type related exceptions
          			try { $this->setData($property->getName(), $value); }
            		catch(AnDomainExceptionType $e) { print $e->getMessage();die; }
          		} 
          		elseif ( !$property ) 
          		{
          			$this->$key = $value;
          		}
        	}
            return $this;
        } 
        else 
        {
        	$name	  	 = $property; 
        	$description = $this->description();      	
        	$property 	 = $description->getProperty($property);
        	
        	if ( !$property ) 
        	{
        		$this->set($name, $value);
        		return $this;
        	}

        	$name 	  = $property->getName();        	
			$method   = 'set'.ucfirst($name);

			if ( $this->methodExists($method) ) 
				$this->$method($value);
			else  
			{
				//only set the property if it's not write proteced (write != private )       		
            	if ( $property->getWriteAccess() < AnDomain::ACCESS_PROTECTED   )
        			throw new KException(get_class($this).'::$'.$name.' is write protected');
        							
				$this->set($name, $value);
			}
				
			return $this;
        }
    }
    
	/**
	 * get the value of a property by checking for custom getter. If no property
	 * is passed an array of properties is returend
	 * 
	 * @param string $property Property name
	 * @param string $default  Default value 
	 * 
	 * @return mixed
	 */	
    public function getData($property = AnDomain::ACCESS_PUBLIC, $default = null)
    {
    	$description	= $this->description();
    	
		if ( gettype($property) == 'integer' )
		{
		    $properties  = $this->description()->getProperty();
		    $access      = (int) $property;
			$data   = array();
			
			foreach($properties as $name => $property)
			{
			    if ( $property->getReadAccess() >= $access )
			    {
			        $data[$name] = $this->getData($name);
			    }
			}			
				
			return $data;
		}
		
		if ( ! $prop = $description->getProperty($property) ) 
		{
			return $this->get($property, $default);
		}
				
		$method 	= 'get'.ucfirst($property);
						
		if ( $this->methodExists($method))  
		{
			$value = $this->$method();
		} else 
		{			
        	$value = $this->get($property);
		}
		
		if ( is_null($value) ) $value = $default;
		
		return $value;
    }
	
	/**
	 * Set a property value {@link self::setData}
	 * 
	 */	
	public function __set($property, $value)
	{
 		if ( $this->description()->getProperty($property) )
 		{
 		    $this->setData($property, $value);
 		}			
		else 
		{		   
			$this->$property = $value;
		}		
	}
	
	/**
	 * Get a property value {@link self::setData}
	 * 
	 * @param string $property	 
	 * @return mixed
	 */		
	public function __get($property)
	{
	   if ( $property == '__space' )
	   {
	       return $this->getRepository()->getSpace();
	   }	   		
	   elseif ( $this->description()->getProperty($property) )
	   {
	       return $this->getData($property, null);
	   }				   
	   else 
	   {	   		
	   	   return null;
	   }
	}

	/**
	 * Check if a property has been set
	 * 
	 * @param string $property	 
	 * @return boolean
	 */		
	public function __isset($property)
	{
		if ( $property = $this->description()->getProperty($property) ) {
			 $name     = $property->getName();
			
			if ( $this->_data->offsetExists($name) ) 
			{
				//if a property is one to one or many to one make sure the value
				//actually exists in the database
				$value = $this->_data->offsetGet($name);
				
				if ( $value instanceof AnDomainEntityProxy  ) 
				{
					if ( $property->isRelationship() && $property->isOneToOne()  ) 
					{
						if ( !$value->getObject() ) 
						{
						    $this->getRepository()->getCommandChain()->disable();
							$this->set($name, null);
							$this->getRepository()->getCommandChain()->enable();
							return false;
						}
					}
				}
				
				return true;
			}
		}
		return false;
	}
    	
	/**
	 * unset the value of a property
	 * @param string $property	
	 */
	public function __unset($property)
	{
		if ( $property = $this->description()->getProperty($property) )
			unset($this->_data[$property->getName()]);
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
	 * To be used instead of the === operator for deep object comparison
	 * 
	 * @param AnDomainAbstractEntity $entity
	 * @return boolean
	 */
	public function eql($entity)
	{
		if ( $entity instanceof AnDomainEntityProxy ) {
			$object = $entity->getObject();
		} else if ( $entity instanceof KMixinAbstract )
			$object = $entity->getMixer();		
		else 
			$object = $entity;
							
		return $this === $object; 
	}
			
	/**
	 * Set the state of the entity to deleted. Not the entity is not persisted but
	 * its state only changed to deleted. 
	 * 
	 * @return boolean
	 */
	public function delete()
	{
		return $this->__space->setState($this, AnDomain::STATE_DELETED);
	}
	
	/**
	 * The entity state is both changed to deleted and it's persistet
	 * 
	 * @return boolean
	 */
	public function destroy()
	{
		if ( $this->delete() )
			return $this->save();
		return false;
	}
								
	/**
	 * Implements magic method. Dynamically mixes a mixin
	 * 
	 * @param string $method Method name
	 * @param array  $args   Array of arugments
	 * 
	 * @return mixed
	 */
	public function __call($method, $args)
	{		
		 //If the method hasn't been mixed yet, load all the behaviors
    	if( !isset($this->_mixed_methods[$method]) ) 
    	{
    		$key = 'behavior.'.$method;
    		
    		if ( !self::_cache($this)->offsetExists($key) )
    		{
    			self::_cache($this)->offsetSet($key, false);
    			
	    		$behaviors = $this->getRepository()->getBehaviors();
	    		
	        	foreach($behaviors as $behavior) 
	        	{
	        		if ( in_array($method, $behavior->getMixableMethods()) ) 
	        		{
	        			//only mix the mixin that has $method
	        			self::_cache($this)->offsetSet($key, $behavior);
	        			break;
	        		}
	        	}
    		}
    		
    		if ( $behavior = self::_cache($this)->offsetGet($key) ) {
    			$this->mixin($behavior);
    		}
        }
        
        $parts = KInflector::explode($method);
        
        if ( $parts[0] == 'is' )
        {
            if(isset($this->_mixed_methods[$method]))          
                return true;            
            else 
                return false;            
        }
		        
        if ( !isset($this->_mixed_methods[$method]) ) 
        {
        	if ( $parts[0] == 'get' || $parts[0] == 'set' )
        	{
        		$property  = lcfirst(KInflector::implode(array_slice($parts, 1)));
        		$property  = $this->description()->getProperty($property);
        		if ( $property ) 
        		{
        			if ( $parts[0] == 'get' )
        				return $this->getData($property->getName());
        			else 
        				return $this->setData($property->getName(), array_shift($args));
        		}
        	}
        }
                
		return parent::__call($method, $args);
	}
	
	/**
	 * Executes a command on entity
	 * 
	 * @param string                $command   The command to execute. It must of form part1.part2
	 * @param KCommandContext|array $context   The command context
	 * 
	 * @return boolean
	 */
	public function execute($command, $context)
	{
        if ( !$context instanceof  KCommandContext ) {
            $context = new KCommandContext($context);
        }
        
		$parts   	= explode('.', $command);
        
		$method     = '_'.$parts[0].'Entity'.ucfirst($parts[1]);        
		$result = null;
				
		if ( method_exists($this, $method)) {
			$result = $this->$method($context);
		}
		
		return $result;
	}
		
    /**
     * Checks if the object or one of it's mixins inherits from a class. 
     * 
     * @param 	string|object 	The class to check
     * @return 	boolean 		Returns TRUE if the object inherits from the class
     */
    public function inherits($class)
    {
    	if ( $this instanceof $class )
    		return true;
    	  		
        if ( !parent::inherits($class) )
        {
        	//check the mixins registered with the entity mapper
        	$behaviors = $this->getRepository()->getBehaviors();
   			
        	foreach($behaviors as $behavior) 
        	{
        		if ( $behavior instanceof $class ) 
        			return true;
        	}
        	
        	return false;
        	
        } else return true;
    }
	
	/**
	 * Get the entity repository
	 * 
	 * @return AnDomainRepositoryAbstract
	 */
	public function getRepository()
	{
	    if ( !$this->_repository instanceof AnDomainRepositoryAbstract )
	    {
	        $this->_repository = $this->getService($this->_repository);
	    }
	    
		return $this->_repository;
	}
	
	/**
	 * Get the entity description
	 * 
	 * @return AnDomainRepositoryAbstract
	 */
	public function description()
	{
		return $this->getRepository()->getDescription();
	}
		
	/**
	 * Inspects an enttiy. $dump is passed as true. the result is passed to the method var_dump
	 * 
	 * @param  boolean $dump Flag to whether dump the data or not
     * 
	 * @return array;
	 */
	public function inspect($dump = true)
	{
		$properties = $this->description()->getProperty();
		$identifier = $this->getIdentifier();
		$data		= array();
		$data = array('identifier'=>$identifier);
		$data['hash']  = $this->getHandle();
		$data['state'] = $this->state();;
		$data['keys']  = implode(',', array_keys($this->description()->getKeys()));
		$data['required']      = array();
		foreach($properties as $name => $property)
		{ 
            $value = isset($this->$name) ? $this->get($name) : null;
			if ( $property->isRequired() )
			    $data['required'][] = $name;
			$value = $value ? $property->serialize($value) : array();		
			$data['data'][$name] = 	count($value) < 2 ? array_pop($value) : $value;
		}
		if ( count($this->_modified) ) {
			foreach($this->modifications() as $property => $changes) {
				$property = $this->description()->getProperty($property);
				$old = $property->serialize($changes->old);
				$new = $property->serialize($changes->new);	
				$data['modified'][$property->getName()] = array('old'=>count($old) < 2 ? array_pop($old) : $old, 'new'=>count($new) < 2 ? array_pop($new) : $new);
			}
		}
			
		if ( count($this->getRowData()) )
			$data['row'] = $this->getRowData();
		
		$serialized = $this->getAffectedRowData();
		
		if ( !empty($serialized) ) {
			$data['serilized'] = $serialized;	
		}
			
		if ( $dump )
			var_dump($data);
		return $data;
	}
	
	/**
	 * Make a clone of the entity with it's attributes. The unique properties are
     * not copied. If deep copy is selected, then this method tries to replicate
     * all the ony-to-many relationships as well
	 * 
     * @param boolean $deep Flag to determine to whether to deep copy.
     * 
	 * @return AnDomainEntityAbstract
	 */   
	public function cloneEntity($deep = true)
	{
        $copy       = $this->getRepository()->getEntity(); 
        $properties = $this->description()->getProperty();
        $data       = array();
        foreach($properties as $property)
        {
            if ( $property->isUnique() )
                continue;
            
            if ( $property === $this->description()->getIdentityProperty() )
                continue;
                
            $name = $property->getName();
            
            if ( $property->isAttribute() ) 
            {
                $copy->set($name, $property->isScalar() ? $this->get($name) : clone $this->get($name));
            }            
            elseif ( $property->isRelationship() )
            {
                //if it's a belongs to then set the value in the 
                //copy as  the original
                if ( $property->isManyToOne() ) 
                {
                    $copy->set($name, $this->get($name));
                }
                //copy the one to one
                elseif ( $deep && $property->isOneToOne() ) 
                {
                    if ( isset($this->$name) ) {
                        $copy->set($name, $this->get($name)->cloneEntity($deep));    
                    }
                }
                //copy the one to many
                elseif( $deep && $property->isOneToMany() && !$property->isManyToMany() )
                {
                    $copy->set($name, $this->get($name)->cloneEntity());
                }
            }
        }

		return $copy;
	}	
	
	/**
	 * Clones a model - this is for when creating a list of models and we don't want to instantiate them
	 * 
	 * @return 
	 */   
	public function __clone()
	{
		$this->_data = new AnDomainEntityData(new KConfig(array('entity'=>$this)));		
	}
		
	/**
	 * Check if method exists between the entities and all its behavior. It caches
	 * the result once for the whole repository in order to improve performance
	 * 
	 * @return boolean
	 */
	public function methodExists($method)
	{
		$key = 'method.'.$method;
		
		if ( !self::_cache($this)->offsetExists($key) )
		{
			$result = false;
			
			if ( method_exists($this, $method) )	
				$result =  true;
			elseif ( self::_cache($this)->offsetExists('behavior.'.$method) )
				$result = self::_cache($this)->offsetGet('behavior.'.$method);
			else 
			{
				$behaviors  = $this->getRepository()->getBehaviors();			
				foreach($behaviors as $behavior) 
				{
					if ( in_array($method, $behavior->getMixableMethods($this)) ) {
						$result = true;
						break;	
					}
				}
			}
			
			self::_cache($this)->offsetSet($key, $result);	
		}
				
		return self::_cache($this)->offsetGet($key);	
	}
	
}