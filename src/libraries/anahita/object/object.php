<?php
/**
 * @package     Anahita_Object
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
class AnObject implements AnObjectHandlable, AnObjectServiceable
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
     * @var AnServiceIdentifier
     */
    private $__service_identifier;

    /**
     * The service container
     *
     * @var AnService
     */
    private $__service_container;

    /**
     * Constructor
     *
     * @param AnConfig|null $config  An optional AnConfig object with configuration options
     * @return \AnObjectDecorator
     */
    public function __construct(AnConfig $config = null)
    {
        //Set the service container
        if (isset($config->service_container)) {
            $this->__service_container = $config->service_container;
        }

        //Set the service identifier
        if (isset($config->service_identifier)) {
            $this->__service_identifier = $config->service_identifier;
        }

        //Initialise the object
        if ($config) {
            $this->_initialize($config);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   AnConfig $object An optional AnConfig object with configuration options
     * @return  void
     */
    protected function _initialize(AnConfig $config)
    {
        //do nothing
    }

    /**
     * Set the object properties
     *
     * @param   string|array|object $property The name of the property, an associative array or an object
     * @param   mixed               $value    The value of the property
     * @throws  AnObjectException If trying to access protected or private properties
     * @return  AnObject
     */
    public function set($property, $value = null)
    {
        if (is_object($property)) {
            $property = get_object_vars($property);
        }

        if (is_array($property)) {
            foreach ($property as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            if ('_' == substr($property, 0, 1)) {
                throw new AnObjectException("Protected or private properties can't be set outside of object scope in ".get_class($this));
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

        if (is_null($property)) {
            $result  = get_object_vars($this);

            foreach ($result as $key => $value) {
                if ('_' == substr($key, 0, 1)) {
                    unset($result[$key]);
                }
            }
        } else {
            if (isset($this->$property)) {
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
     * @param   AnMixinInterface  $object An object that implements KMinxInterface
     * @return  AnObject
     */
    public function mixin(AnMixinInterface $object, $config = array())
    {
        if (!$object instanceof AnMixinInterface) {
            if (!$object instanceof AnServiceIdentifier) {
                //Create the complete identifier if a partial identifier was passed
                if (is_string($object) && strpos($object, '.') === false) {
                    $identifier = clone $this->getIdentifier();
                    $identifier->path = 'mixin';
                    $identifier->name = $object;
                } else {
                    $identifier = $this->getIdentifier($object);
                }
            } else {
                $identifier = $object;
            }

            $config = new AnConfig($config);
            $config->mixer = $this;
            $object = new $identifier->classname($config);
            if (!$object instanceof AnMixinInterface) {
                throw new \UnexpectedValueException(
                        'Mixin: '.get_class($mixin).' does not implement AnMixinInterface'
                );
            }
        }
        $methods = $object->getMixableMethods($this);

        foreach ($methods as $method) {
            $this->_mixed_methods[$method] = $object;
            if (!empty($this->__methods) && !in_array($method, $this->__methods)) {
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

        foreach ($objects as $object) {
            if ($object instanceof $class) {
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
        return spl_object_hash($this);
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
        if (!$this->__methods) {
            $methods = array();

            $reflection = new ReflectionClass($this);
            foreach ($reflection->getMethods() as $method) {
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
     * @throws	AnObjectException if the service container has not been defined.
     * @return	object  		Return object on success, throws exception on failure
     * @see 	AnObjectServiceable
     */
    final public function getService($identifier = null, array $config = array())
    {
        if (!isset($this->__service_container)) {
            throw new AnObjectException("Failed to call ".get_class($this)."::getService(). No service_container object defined.");
        }
        if (!isset($identifier)) {
            $result =  $this->__service_container;
        } else {
            $result =  $this->__service_container->get($identifier, $config);
        }
        return $result;
    }

    /**
     * Gets the service identifier.
     *
     * @param	string|object	$identifier The class identifier or identifier object
     * @throws	AnObjectException if the service container has not been defined.
     * @return	AnServiceIdentifier
     * @see 	AnObjectServiceable
     */
    final public function getIdentifier($identifier = null)
    {
        if (isset($identifier)) {
            if (!isset($this->__service_container)) {
                throw new AnObjectException("Failed to call ".get_class($this)."::getIdentifier(). No service_container object defined.");
            }

            $result = $this->__service_container->getIdentifier($identifier);
        } else {
            $result = $this->__service_identifier;
        }

        return $result;
    }

    /**
     * Preform a deep clone of the object.
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->_mixed_methods as $method => $object) {
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
        if (isset($this->_mixed_methods[$method])) {
            $object = $this->_mixed_methods[$method];
            $result = null;

            //Switch the mixin's attached mixer
            $object->setMixer($this);

            // Call_user_func_array is ~3 times slower than direct method calls.
            switch (count($arguments)) {
                case 0:
                    $result = $object->$method();
                    break;
                case 1:
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
