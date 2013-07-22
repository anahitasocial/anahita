<?php
/**
 * @version		$Id: object.php 4647 2012-05-13 21:28:58Z johanjanssens $
 * @package		Koowa_Object
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Object class
 *
 * Provides getters and setters, mixin, object handles
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Object
 */
class KObject implements KObjectHandlable, KObjectServiceable
{
    /**
     * Class methods
     *
     * @var array
     */
    private $__methods = array();

    /**
     * Mixed in methods
     *
     * @var array
     */
    protected $_mixed_methods = array();

    /**
     * The service identifier
     *
     * @var KServiceIdentifier
     */
    private $__service_identifier;

    /**
     * The service container
     *
     * @var KService
     */
    private $__service_container;

    /**
     * Constructor
     *
     * @param KConfig|null $config  An optional KConfig object with configuration options
     * @return \KObjectDecorator
     */
    public function __construct( KConfig $config = null)
    {
        //Set the service container
        if(isset($config->service_container)) {
            $this->__service_container = $config->service_container;
        }

        //Set the service identifier
        if(isset($config->service_identifier)) {
            $this->__service_identifier = $config->service_identifier;
        }

        //Initialise the object
        if($config) {
            $this->_initialize($config);
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
        //do nothing
    }

    /**
     * Set the object properties
     *
     * @param   string|array|object $property The name of the property, an associative array or an object
     * @param   mixed               $value    The value of the property
     * @throws  KObjectException If trying to access protected or private properties
     * @return  KObject
     */
    public function set( $property, $value = null )
    {
        if(is_object($property)) {
            $property = get_object_vars($property);
        }

        if(is_array($property))
        {
            foreach ($property as $k => $v) {
                $this->set($k, $v);
            }
        }
        else
        {
            if('_' == substr($property, 0, 1)) {
                throw new KObjectException("Protected or private properties can't be set outside of object scope in ".get_class($this));
            }

            $this->$property = $value;
        }

        return $this;
    }

    /**
     * Get the object properties
     *
     * If no property name is given then the function will return an associative
     * array of all properties.
     *
     * If the property does not exist and a  default value is specified this is
     * returned, otherwise the function return NULL.
     *
     * @param   string  $property The name of the property
     * @param   mixed   $default  The default value
     * @return  mixed   The value of the property, an associative array or NULL
     */
    public function get($property = null, $default = null)
    {
        $result = $default;

        if(is_null($property))
        {
            $result  = get_object_vars($this);

            foreach ($result as $key => $value)
            {
                if ('_' == substr($key, 0, 1)) {
                    unset($result[$key]);
                }
            }
        }
        else
        {
            if(isset($this->$property)) {
                $result = $this->$property;
            }
        }

        return $result;
    }

    /**
     * Mixin an object
     *
     * When using mixin(), the calling object inherits the methods of the mixed
     * in objects, in a LIFO order.
     *
     * @param   KMixinInterface  $object An object that implements KMinxInterface
     * @return  KObject
     */
    public function mixin(KMixinInterface $object)
    {
        $methods = $object->getMixableMethods($this);

        foreach($methods as $method) {            
            $this->_mixed_methods[$method] = $object;
            if ( !empty($this->__methods) && !in_array($method, $this->__methods) ) 
            {
                $this->__methods[] = $method;
            }
        }

        //Set the mixer
        $object->setMixer($this);

        return $this;
    }

    /**
     * Checks if the object or one of it's mixin's inherits from a class.
     *
     * @param   string|object   $class The class to check
     * @return  bool Returns TRUE if the object inherits from the class
     */
    public function inherits($class)
    {
        if ($this instanceof $class) {
            return true;
        }

        $objects = array_values($this->_mixed_methods);

        foreach($objects as $object)
        {
            if($object instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a handle for this object
     *
     * This function returns an unique identifier for the object. This id can be used as
     * a hash key for storing objects or for identifying an object
     *
     * @return string A string that is unique
     */
    public function getHandle()
    {
        return spl_object_hash( $this );
    }

    /**
     * Get a list of all the available methods
     *
     * This function returns an array of all the methods, both native and mixed in
     *
     * @return array An array
     */
    public function getMethods()
    {
        if(!$this->__methods)
        {
            $methods = array();

            $reflection = new ReflectionClass($this);
            foreach($reflection->getMethods() as $method) {
                $methods[] = $method->name;
            }

            $this->__methods = array_merge($methods, array_keys($this->_mixed_methods));
        }

        return $this->__methods;
    }

	/**
	 * Get an instance of a class based on a class identifier only creating it
	 * if it does not exist yet.
	 *
	 * @param	string|object	$identifier The class identifier or identifier object
	 * @param	array  			$config     An optional associative array of configuration settings.
	 * @throws	KObjectException if the service container has not been defined.
	 * @return	object  		Return object on success, throws exception on failure
	 * @see 	KObjectServiceable
	 */
	final public function getService($identifier, array $config = array())
	{
	    if(!isset($this->__service_container)) {
	        throw new KObjectException("Failed to call ".get_class($this)."::getService(). No service_container object defined.");
	    }

	    return $this->__service_container->get($identifier, $config);
	}

	/**
	 * Gets the service identifier.
	 *
	 * @param	string|object	$identifier The class identifier or identifier object
     * @throws	KObjectException if the service container has not been defined.
	 * @return	KServiceIdentifier
	 * @see 	KObjectServiceable
	 */
	final public function getIdentifier($identifier = null)
	{
		if(isset($identifier))
		{
		    if(!isset($this->__service_container)) {
	            throw new KObjectException("Failed to call ".get_class($this)."::getIdentifier(). No service_container object defined.");
	        }

		    $result = $this->__service_container->getIdentifier($identifier);
		}
		else  $result = $this->__service_identifier;

	    return $result;
	}

	/**
     * Preform a deep clone of the object.
     *
     * @return void
     */
    public function __clone()
    {
        foreach($this->_mixed_methods as $method => $object) {
            $this->_mixed_methods[$method] = clone $object;
        }
    }

    /**
     * Search the mixin method map and call the method or trigger an error
     *
     * @param  string $method    The function name
     * @param  array  $arguments The function arguments
     * @throws BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        if(isset($this->_mixed_methods[$method]))
        {
            $object = $this->_mixed_methods[$method];
            $result = null;

            //Switch the mixin's attached mixer
            $object->setMixer($this);

            // Call_user_func_array is ~3 times slower than direct method calls.
            switch(count($arguments))
            {
                case 0 :
                    $result = $object->$method();
                    break;
                case 1 :
                    $result = $object->$method($arguments[0]);
                    break;
                case 2:
                    $result = $object->$method($arguments[0], $arguments[1]);
                    break;
                case 3:
                    $result = $object->$method($arguments[0], $arguments[1], $arguments[2]);
                    break;
                default:
                    // Resort to using call_user_func_array for many segments
                    $result = call_user_func_array(array($object, $method), $arguments);
             }

            return $result;
        }

        throw new BadMethodCallException('Call to undefined method :'.$method);
    }
}